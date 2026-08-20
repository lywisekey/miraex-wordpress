<?php
/**
 * Import reference-site images into the WP media library.
 * Idempotent: skips files already imported (matched by _miraex_src_url meta).
 */
require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

wp_set_current_user(1);

$base = 'https://ashy-forest-0b7587303.7.azurestaticapps.net/';

$files = [
	'assets/img/miraex-logo-full.png'   => 'Miraex — Connecting Quantum (logo)',
	'assets/img/miraex-logo.png'        => 'Miraex logo',
	'assets/img/favicon.png'            => 'Miraex favicon',
	'assets/img/hero-photonics.jpg'     => 'A beam of light splitting into a spectrum of cyan and violet — quantum information carried on photons',
	'assets/img/photon-journey.jpg'     => 'Microwave waves entering a photonic chip on the left and emerging as an optical beam on the right',
	'assets/img/pic-chip.jpg'           => 'Macro render of a thin-film lithium tantalate photonic integrated circuit with gold waveguides',
	'assets/img/orbital-qkd.jpg'        => 'Satellite quantum link over Earth',
	'assets/img/cleanroom.jpg'          => 'Cleanroom fabrication',
	'assets/img/cryostat.jpg'           => 'Quantum hardware',
	'assets/img/logos/epfl.png'                     => 'EPFL',
	'assets/img/logos/innosuisse.png'               => 'Innosuisse',
	'assets/img/logos/innovaud.png'                 => 'Innovaud',
	'assets/img/logos/esa-bic.png'                  => 'ESA BIC',
	'assets/img/logos/ibm-q-network.png'            => 'IBM Q Network',
	'assets/img/logos/venture-kick.png'             => 'Venture Kick',
	'assets/img/logos/venturelab.png'               => 'Venturelab',
	'assets/img/logos/fit.png'                      => 'FIT',
	'assets/img/logos/swiss-pic.png'                => 'Swiss PIC',
	'assets/img/logos/swissnex.png'                 => 'Swissnex',
	'assets/img/logos/imd.svg'                      => 'IMD',
	'assets/img/logos/epic.png'                     => 'EPIC',
	'assets/img/logos/top100-swiss-startup.png'     => 'TOP100 Swiss Startup',
	'assets/img/logos/creative-destruction-lab.png' => 'Creative Destruction Lab',
];

$map = [];

foreach ( $files as $path => $alt ) {
	$url = $base . $path;

	$existing = get_posts([
		'post_type'   => 'attachment',
		'numberposts' => 1,
		'fields'      => 'ids',
		'meta_key'    => '_miraex_src_url',
		'meta_value'  => $url,
	]);

	if ( $existing ) {
		$id = (int) $existing[0];
		$map[ $path ] = [ 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'status' => 'exists' ];
		continue;
	}

	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) {
		$map[ $path ] = [ 'error' => $tmp->get_error_message() ];
		continue;
	}

	$file_array = [ 'name' => basename( $path ), 'tmp_name' => $tmp ];
	$id = media_handle_sideload( $file_array, 0, $alt );

	if ( is_wp_error( $id ) ) {
		@unlink( $tmp );
		$map[ $path ] = [ 'error' => $id->get_error_message() ];
		continue;
	}

	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	update_post_meta( $id, '_miraex_src_url', $url );

	$map[ $path ] = [ 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'status' => 'imported' ];
}

file_put_contents( '/tmp/media-map.json', json_encode( $map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
echo json_encode( $map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n";
