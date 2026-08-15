<?php
/**
 * Install and activate LiteSpeed Cache.
 *
 * Its page cache only works when the site is served by LiteSpeed or OpenLiteSpeed —
 * on nginx (this laradock environment) that half of the plugin is inert, while the
 * optimisation half (minify, lazy load, browser cache headers) still applies. The
 * target host runs LiteSpeed, which is why this plugin rather than a PHP-level cache.
 *
 * Idempotent.
 */

require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

$slug = 'litespeed-cache';
$file = $slug . '/litespeed-cache.php';

if ( ! file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {

	/* direct filesystem: the upgrader otherwise asks for FTP credentials and returns
	   false without saying why on the CLI */
	add_filter( 'filesystem_method', function () { return 'direct'; } );
	WP_Filesystem();

	$skin     = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( 'https://downloads.wordpress.org/plugin/' . $slug . '.zip' );

	if ( is_wp_error( $result ) || ! $result ) {
		$why = is_wp_error( $result ) ? $result->get_error_message() : implode( ' ', (array) $skin->get_upgrade_messages() );
		fwrite( STDERR, "install failed: " . $why . "\n" );
		exit( 1 );
	}

	echo "downloaded and unpacked\n";
} else {
	echo "already present\n";
}

if ( ! is_plugin_active( $file ) ) {
	$activated = activate_plugin( $file );

	if ( is_wp_error( $activated ) ) {
		fwrite( STDERR, "activation failed: " . $activated->get_error_message() . "\n" );
		exit( 1 );
	}

	echo "activated\n";
} else {
	echo "already active\n";
}

$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file );
printf( "%s %s\n", $data['Name'], $data['Version'] );
printf( "active plugins: %s\n", implode( ', ', get_option( 'active_plugins' ) ) );
