<?php
/**
 * Install and activate Rank Math from wordpress.org.
 * Idempotent: skips the download if the plugin folder is already there.
 */

require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

$slug = 'seo-by-rank-math';
$file = $slug . '/rank-math.php';

if ( ! file_exists( WP_PLUGIN_DIR . '/' . $slug ) ) {

	/* direct filesystem: there is no FTP layer in this install and the upgrader
	   otherwise asks for credentials and silently returns false on the CLI */
	add_filter( 'filesystem_method', function () { return 'direct'; } );
	WP_Filesystem();

	$skin     = new WP_Ajax_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( 'https://downloads.wordpress.org/plugin/' . $slug . '.zip' );

	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, "install failed: " . $result->get_error_message() . "\n" );
		exit( 1 );
	}

	if ( ! $result ) {
		$errors = $skin->get_errors();
		fwrite( STDERR, "install failed: "
			. ( is_wp_error( $errors ) && $errors->get_error_message() ? $errors->get_error_message() : 'no reason given' )
			. "\n" . implode( "\n", (array) $skin->get_upgrade_messages() ) . "\n" );
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
