<?php
/**
 * Loads WordPress for the CLI builders.
 *
 * The path to wp-load.php differs on every machine (and inside a container it is not
 * the path you see on the host), so it is discovered rather than hard-coded:
 *
 *   1. $MIRAEX_WP_ROOT, if set — the escape hatch for unusual layouts
 *   2. otherwise, walk up from this file until wp-load.php appears
 *
 * Usage:  require __DIR__ . '/bootstrap.php';
 */

if ( defined( 'ABSPATH' ) ) {
	return;
}

$root = getenv( 'MIRAEX_WP_ROOT' );

if ( ! $root ) {
	$dir = __DIR__;

	while ( $dir && '/' !== $dir ) {
		if ( file_exists( $dir . '/wp-load.php' ) ) {
			$root = $dir;
			break;
		}

		$dir = dirname( $dir );
	}
}

if ( ! $root || ! file_exists( rtrim( $root, '/' ) . '/wp-load.php' ) ) {
	fwrite( STDERR, "Could not find wp-load.php above " . __DIR__ . ".\n"
		. "Run this from inside the WordPress install, or set MIRAEX_WP_ROOT=/path/to/wordpress.\n" );
	exit( 1 );
}

require rtrim( $root, '/' ) . '/wp-load.php';

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "wp-load.php did not bootstrap WordPress.\n" );
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/post.php';

/* every builder writes as the site owner */
wp_set_current_user( 1 );
