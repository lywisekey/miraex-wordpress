<?php
/**
 * Server-side contact endpoint.
 *
 * The form used to POST straight to HubSpot from the browser, which meant the portal id
 * and form GUID were in the page source and HubSpot accepted a POST from any origin —
 * so anything checked in the browser (honeypot, captcha, rate limit) could be skipped by
 * posting to HubSpot directly. Everything that decides whether a submission is genuine
 * now runs here, where a client cannot reach it, and the HubSpot identifiers never leave
 * the server.
 *
 * Turnstile is optional: define both constants in wp-config.php and verification turns
 * itself on. Without them the endpoint still applies the honeypot, the field checks and
 * the rate limit.
 *
 *   define( 'MIRAEX_TURNSTILE_SITEKEY', '...' );
 *   define( 'MIRAEX_TURNSTILE_SECRET',  '...' );
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MIRAEX_HS_PORTAL' ) ) {
	define( 'MIRAEX_HS_PORTAL', '9201667' );
}

if ( ! defined( 'MIRAEX_HS_FORM' ) ) {
	define( 'MIRAEX_HS_FORM', 'f1490e22-9dbb-479d-8c82-79d87fb33f1a' );
}

/**
 * Submissions forwarded to HubSpot from one address per hour, and per day.
 *
 * Generous on purpose: a whole office shares one address, so a limit tuned to a single
 * person turns colleagues away. It exists to stop volume, not to ration enquiries.
 */
const MIRAEX_RATE_HOUR = 10;
const MIRAEX_RATE_DAY  = 30;

/** HubSpot property names, confirmed against the live form. */
function miraex_contact_fields() {
	return [
		'firstname'     => [ 'required' => true ],
		'lastname'      => [ 'required' => true ],
		'email'         => [ 'required' => true, 'email' => true ],
		'company'       => [ 'required' => false ],
		'miraex_intent' => [ 'required' => true ],
		'comments'      => [ 'required' => true ],
	];
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'miraex/v1', '/contact', [
		'methods'             => 'POST',
		'callback'            => 'miraex_contact_submit',
		'permission_callback' => '__return_true',
	] );
} );

function miraex_client_ip() {
	/* Behind a proxy or CDN the socket address is the edge, not the visitor. Only the
	   first hop of X-Forwarded-For is worth reading, and only when a proxy is expected —
	   a client can forge the header when nothing in front strips it.
	 *
	 * On AWS behind an ALB or CloudFront, `define( 'MIRAEX_BEHIND_PROXY', true )` is not
	 * optional: without it every visitor arrives as the balancer, they all share one
	 * rate-limit bucket, and HubSpot scores spam against the wrong address. Nothing
	 * errors — the eleventh message of the hour from anybody is simply refused. */
	if ( defined( 'MIRAEX_BEHIND_PROXY' ) && MIRAEX_BEHIND_PROXY && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		$ip    = trim( $parts[0] );

		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}

	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

/** True when this address has already sent as much as it is allowed to. */
function miraex_rate_limited( $ip ) {
	if ( ! $ip ) {
		return false;
	}

	$key = substr( hash( 'sha256', $ip . wp_salt() ), 0, 24 );

	foreach ( [ [ 'h', HOUR_IN_SECONDS, MIRAEX_RATE_HOUR ], [ 'd', DAY_IN_SECONDS, MIRAEX_RATE_DAY ] ] as $window ) {
		list( $suffix, $ttl, $max ) = $window;

		$name  = 'miraex_rate_' . $suffix . '_' . $key;
		$count = (int) get_transient( $name );

		if ( $count >= $max ) {
			return true;
		}

		set_transient( $name, $count + 1, $ttl );
	}

	return false;
}

function miraex_turnstile_ok( $token, $ip ) {
	if ( ! defined( 'MIRAEX_TURNSTILE_SECRET' ) || ! MIRAEX_TURNSTILE_SECRET ) {
		return true;   /* not configured — the other checks stand on their own */
	}

	if ( ! $token ) {
		return false;
	}

	$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
		'timeout' => 10,
		'body'    => [
			'secret'   => MIRAEX_TURNSTILE_SECRET,
			'response' => $token,
			'remoteip' => $ip,
		],
	] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return ! empty( $body['success'] );
}

function miraex_contact_submit( WP_REST_Request $request ) {
	$ip = miraex_client_ip();

	/* The trap is checked here, not in the browser, so a bot cannot simply omit it.
	   A filled trap gets the same answer as a real submission — telling a bot it was
	   caught only teaches it what to change. */
	if ( trim( (string) $request->get_param( 'mx_r9' ) ) !== '' ) {
		return new WP_REST_Response( [ 'ok' => true ], 200 );
	}

	if ( ! miraex_turnstile_ok( $request->get_param( 'turnstile' ), $ip ) ) {
		return new WP_REST_Response( [
			'ok'      => false,
			'message' => 'The verification check did not pass. Please reload the page and try again.',
		], 400 );
	}

	$fields = [];

	foreach ( miraex_contact_fields() as $name => $rule ) {
		$value = trim( (string) $request->get_param( $name ) );

		if ( '' === $value ) {
			if ( ! empty( $rule['required'] ) ) {
				return new WP_REST_Response( [
					'ok'      => false,
					'message' => 'Please fill in every required field.',
				], 400 );
			}

			continue;
		}

		if ( ! empty( $rule['email'] ) && ! is_email( $value ) ) {
			return new WP_REST_Response( [
				'ok'      => false,
				'message' => 'That email address does not look right.',
			], 400 );
		}

		$fields[] = [ 'name' => $name, 'value' => sanitize_textarea_field( $value ) ];
	}

	$fields[] = [ 'name' => 'consent', 'value' => 'true' ];

	/* Counted here rather than at the top of the request: what is worth rationing is
	   submissions that reach HubSpot, not malformed posts, which cost nothing and would
	   otherwise let a mistyped email burn a real visitor's allowance. */
	if ( miraex_rate_limited( $ip ) ) {
		return new WP_REST_Response( [
			'ok'      => false,
			'message' => 'That is a lot of messages from one connection. Please email info@miraex.com instead.',
		], 429 );
	}

	$context = [
		'pageUri'  => esc_url_raw( (string) $request->get_param( 'pageUri' ) ),
		'pageName' => sanitize_text_field( (string) $request->get_param( 'pageName' ) ),
	];

	/* Without this every submission looks to HubSpot as if it came from the web server,
	   which blinds its own spam scoring. */
	if ( $ip ) {
		$context['ipAddress'] = $ip;
	}

	$hutk = sanitize_text_field( (string) $request->get_param( 'hutk' ) );

	if ( $hutk ) {
		$context['hutk'] = $hutk;
	}

	$response = wp_remote_post(
		'https://api.hsforms.com/submissions/v3/integration/submit/' . MIRAEX_HS_PORTAL . '/' . MIRAEX_HS_FORM,
		[
			'timeout' => 15,
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( [ 'fields' => $fields, 'context' => $context ] ),
		]
	);

	if ( is_wp_error( $response ) ) {
		return new WP_REST_Response( [
			'ok'      => false,
			'message' => 'We could not reach our systems. Please email info@miraex.com.',
		], 502 );
	}

	$code = wp_remote_retrieve_response_code( $response );

	if ( $code < 200 || $code > 299 ) {
		/* HubSpot's reason is useful to whoever maintains the site and meaningless to a
		   visitor, so it goes to the log rather than the screen. */
		error_log( 'miraex contact: HubSpot returned ' . $code . ' ' . wp_remote_retrieve_body( $response ) );

		return new WP_REST_Response( [
			'ok'      => false,
			'message' => 'That did not go through. Please email info@miraex.com and we will pick it up there.',
		], 502 );
	}

	return new WP_REST_Response( [ 'ok' => true ], 200 );
}

/** Exposes the endpoint and, when configured, the Turnstile site key to the form. */
add_action( 'wp_head', function () {
	if ( ! is_page() ) {
		return;
	}

	$config = [ 'endpoint' => esc_url_raw( rest_url( 'miraex/v1/contact' ) ) ];

	if ( defined( 'MIRAEX_TURNSTILE_SITEKEY' ) && MIRAEX_TURNSTILE_SITEKEY ) {
		$config['turnstile'] = MIRAEX_TURNSTILE_SITEKEY;
	}

	printf( "<script>window.MIRAEX_CONTACT=%s;</script>\n", wp_json_encode( $config ) );

	/* Explicit rendering, because the widget has to be created by the form's own script
	   once it knows the site key — the form markup is generated ahead of time and cannot
	   carry it. */
	if ( isset( $config['turnstile'] ) ) {
		echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>' . "\n";
	}
}, 5 );
