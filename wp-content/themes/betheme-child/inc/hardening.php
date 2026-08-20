<?php
/**
 * Stop announcing what the site is built on.
 *
 * Read this before expecting too much of it: **you cannot hide WordPress from anyone who
 * looks properly.** Cookie names, the login redirect, the shape of the markup and a dozen
 * other traces give it away, and every scanner worth worrying about checks those. What is
 * worth removing is the **version numbers**, because that is what turns a broad scan into
 * a targeted one — a bot looking for "WordPress 7.0.4 with plugin X" skips a site that
 * does not volunteer the number.
 *
 * So: remove the version tells and the pointless discovery links, keep the REST API
 * working (the contact form depends on it), and do not rename wp-content — the image URLs
 * live inside PHP-serialized builder data, where a rename is the same length-prefix trap
 * as a domain change, for no real gain.
 */

defined( 'ABSPATH' ) || exit;

/* The one that names the exact version. */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/* Editors nobody has used since 2010, and a manifest for Windows Live Writer. */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/* Shortlinks are a WordPress signature and nothing here uses them. */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'template_redirect', 'wp_shortlink_header', 11 );

/* oEmbed discovery: this site is not embedded anywhere as an oEmbed provider. */
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_add_host_js' );

/* The REST link tags advertise the API. The API itself stays on — /wp-json/miraex/v1/contact
   is what the contact form posts to. */
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );

/* PHP's own banner. Removing it here covers hosts that do not set expose_php=Off. */
add_action( 'send_headers', function () {
	if ( ! headers_sent() ) {
		header_remove( 'X-Powered-By' );
		header_remove( 'X-Pingback' );
	}
}, 1 );

/**
 * Emoji support pulls in a script and an inline blob on every page, and both are a
 * WordPress fingerprint. Nothing on this site uses emoji.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
 * Strip the version query from stylesheet and script URLs.
 *
 * Only WordPress's own version is removed — a plugin or theme version is left alone,
 * because it is what busts the browser cache when a file changes, and this site tells
 * browsers to keep assets for a year.
 */
function miraex_strip_wp_version( $src ) {
	global $wp_version;

	if ( $src && false !== strpos( $src, 'ver=' . $wp_version ) ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}
add_filter( 'style_loader_src', 'miraex_strip_wp_version', 9999 );
add_filter( 'script_loader_src', 'miraex_strip_wp_version', 9999 );
