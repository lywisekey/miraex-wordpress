<?php
/**
 * LiteSpeed Cache settings for this site.
 *
 * The reasoning behind each group is in the comments — the defaults are tuned for a
 * blog that changes hourly, this is a 15-page brochure whose pages change when someone
 * re-runs a builder. Anything that rewrites markup is treated as a risk and only turned
 * on where it was verified not to change the rendered page.
 *
 * Page caching needs the site to be served by LiteSpeed/OpenLiteSpeed. On nginx the
 * settings are stored and inert; on the target host they take effect immediately.
 *
 * Idempotent.
 */

require __DIR__ . '/bootstrap.php';

if ( ! defined( 'LSCWP_V' ) && ! class_exists( 'LiteSpeed\Core' ) ) {
	fwrite( STDERR, "LiteSpeed Cache is not active — run install_litespeed.php first.\n" );
	exit( 1 );
}

$conf = [

	/* ---- cache -------------------------------------------------------- */

	'cache'                => 1,
	/* A week. The pages are generated and change only when a builder is re-run, and
	   any edit purges the cache anyway. */
	'cache-ttl_pub'        => 604800,
	'cache-ttl_frontpage'  => 604800,
	/* Long-lived headers for /wp-content assets; their URLs carry a version query. */
	'cache-browser'        => 1,
	'cache-ttl_browser'    => 31557600,
	/* One HTML for every screen: the responsive work is CSS, and all three header
	   variants ship in the same markup. A separate mobile cache would double the
	   entries and buy nothing. */
	'cache-mobile'         => 0,
	/* The contact endpoint is POST and never cached, but a cached GET under /wp-json/
	   would be a trap for whoever adds an endpoint later. */
	'cache-rest'           => 0,
	'cache-exc'            => [ '/wp-json/' ],

	/* ---- optimisation ------------------------------------------------- */

	/* Minify only. Combining is what breaks BeTheme: it loads a dozen scripts that
	   assume their own order and their own inline bootstraps. */
	'optm-css_min'         => 1,
	'optm-js_min'          => 1,
	'optm-css_comb'        => 0,
	'optm-js_comb'         => 0,
	/* Deferring BeTheme's scripts breaks its init; leave the order alone. */
	'optm-js_defer'        => 0,
	'optm-html_min'        => 1,
	/* Critical CSS and unused-CSS removal both need QUIC.cloud and both routinely
	   strip styles from builder pages. Off until someone can watch the result. */
	'optm-css_async'       => 0,
	'optm-ucss'            => 0,
	/* WordPress's emoji script is dead weight on a site that uses none. */
	'optm-emoji_rm'        => 1,

	/* ---- media -------------------------------------------------------- */

	/* The heaviest thing on the site: ~2.8 MB of images on the front page. */
	'media-lazy'           => 1,
	'media-iframe_lazy'    => 1,
	/* Never lazy-load what is on screen at first paint — it delays the largest paint
	   instead of helping it. */
	'media-lazy_exc'       => [ 'miraex-logo', 'hero-photonics' ],
	/* Placeholders need QUIC.cloud. */
	'media-lqip'           => 0,
	'media-vpi'            => 0,

	/* ---- housekeeping -------------------------------------------------- */

	'db_optm-revisions_max' => 10,
	'db_optm-revisions_age' => 180,
	/* Nothing here needs a heartbeat every 15s. */
	'misc-heartbeat_front'     => 1,
	'misc-heartbeat_front_ttl' => 120,
];

$changed = 0;

foreach ( $conf as $key => $value ) {
	$name = 'litespeed.conf.' . $key;
	$old  = get_option( $name );

	/* List settings are stored as arrays, scalars as strings — comparing and writing
	   the wrong shape leaves a setting that looks right in the database and is ignored
	   by the plugin. */
	$same = is_array( $value )
		? ( is_array( $old ) && $old === $value )
		: ( ! is_array( $old ) && (string) $old === (string) $value );

	if ( $same ) {
		continue;
	}

	update_option( $name, $value );
	printf( "  %-26s %s -> %s\n", $key, is_array( $old ) ? '[' . implode( ',', $old ) . ']' : var_export( $old, true ),
		is_array( $value ) ? '[' . implode( ',', $value ) . ']' : var_export( $value, true ) );
	$changed++;
}

printf( "\n%d settings changed\n", $changed );

/* A stale cache after a settings change is the classic way to "prove" nothing worked. */
if ( class_exists( 'LiteSpeed\Purge' ) ) {
	do_action( 'litespeed_purge_all' );
	echo "cache purged\n";
}

echo "\nNote: page caching requires LiteSpeed/OpenLiteSpeed. This server reports: "
	. ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : 'unknown (CLI)' ) . "\n";
