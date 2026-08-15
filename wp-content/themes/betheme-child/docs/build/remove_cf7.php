<?php
/**
 * Remove Contact Form 7. The contact page posts to HubSpot now (see lib_hubspot.php),
 * so the plugin has no page left to serve.
 *
 * CF7 does not store submissions — it emails them — so nothing is lost with it. The
 * orphaned form post is deleted too; what the form contained is documented in
 * ../DEPLOY.md §5.
 */

require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

$file = 'contact-form-7/wp-contact-form-7.php';

/* refuse to run while a page still references the shortcode or the cf7 element */
$in_use = [];

foreach ( get_posts([ 'post_type' => [ 'page', 'post' ], 'numberposts' => -1, 'post_status' => 'any' ]) as $p ) {
	$items = get_post_meta( $p->ID, 'mfn-page-items', true );

	if ( is_array( $items ) && false !== strpos( serialize( $items ), '"cf7"' ) ) {
		$in_use[] = $p->post_title;
	}

	if ( false !== strpos( $p->post_content, '[contact-form-7' ) ) {
		$in_use[] = $p->post_title . ' (shortcode)';
	}
}

if ( $in_use ) {
	fwrite( STDERR, "still in use by: " . implode( ', ', $in_use ) . "\nAborting.\n" );
	exit( 1 );
}

echo "no page uses Contact Form 7\n";

if ( is_plugin_active( $file ) ) {
	deactivate_plugins( $file );
	echo "deactivated\n";
}

foreach ( get_posts([ 'post_type' => 'wpcf7_contact_form', 'numberposts' => -1, 'post_status' => 'any' ]) as $form ) {
	wp_delete_post( $form->ID, true );
	printf( "deleted form post #%d %s\n", $form->ID, $form->post_title );
}

if ( file_exists( WP_PLUGIN_DIR . '/contact-form-7' ) ) {
	add_filter( 'filesystem_method', function () { return 'direct'; } );
	WP_Filesystem();

	$deleted = delete_plugins( [ $file ] );

	if ( is_wp_error( $deleted ) ) {
		fwrite( STDERR, "delete failed: " . $deleted->get_error_message() . "\n" );
		exit( 1 );
	}

	echo "plugin files removed\n";
}

printf( "active plugins: %s\n", implode( ', ', get_option( 'active_plugins' ) ) );
