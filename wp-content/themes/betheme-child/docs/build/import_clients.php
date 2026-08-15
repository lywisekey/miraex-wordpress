<?php
require __DIR__ . '/bootstrap.php';
wp_set_current_user(1);

$base = 'https://ashy-forest-0b7587303.7.azurestaticapps.net/';

$clients = [
	['EPFL', 'assets/img/logos/epfl.png', 'https://www.epfl.ch/'],
	['Innosuisse', 'assets/img/logos/innosuisse.png', 'https://www.innosuisse.ch/'],
	['Innovaud', 'assets/img/logos/innovaud.png', 'https://innovaud.ch/'],
	['ESA BIC', 'assets/img/logos/esa-bic.png', 'https://www.esa.int/'],
	['IBM Q Network', 'assets/img/logos/ibm-q-network.png', 'https://www.ibm.com/quantum'],
	['Venture Kick', 'assets/img/logos/venture-kick.png', 'https://www.venturekick.ch/'],
	['Venturelab', 'assets/img/logos/venturelab.png', 'https://www.venturelab.swiss/'],
	['FIT', 'assets/img/logos/fit.png', 'https://www.fondation-fit.ch/'],
	['Swiss PIC', 'assets/img/logos/swiss-pic.png', ''],
	['Swissnex', 'assets/img/logos/swissnex.png', 'https://swissnex.org/'],
	['IMD', 'assets/img/logos/imd.svg', 'https://www.imd.org/'],
	['EPIC', 'assets/img/logos/epic.png', 'https://epic-assoc.com/'],
	['TOP100 Swiss Startup', 'assets/img/logos/top100-swiss-startup.png', 'https://www.top100startups.swiss/'],
	['Creative Destruction Lab', 'assets/img/logos/creative-destruction-lab.png', 'https://creativedestructionlab.com/'],
];

// map source url -> attachment id
$map = [];
foreach ( get_posts(['post_type'=>'attachment','numberposts'=>-1,'meta_key'=>'_miraex_src_url']) as $a ) {
	$map[ str_replace($base, '', get_post_meta($a->ID, '_miraex_src_url', true)) ] = $a->ID;
}

$order = 1;
foreach ( $clients as $c ) {
	list($name, $path, $link) = $c;

	$existing = get_posts(['post_type'=>'client','name'=>sanitize_title($name),'numberposts'=>1,'post_status'=>'any']);
	$id = $existing ? $existing[0]->ID : 0;

	$data = [
		'post_type'   => 'client',
		'post_title'  => $name,
		'post_name'   => sanitize_title($name),
		'post_status' => 'publish',
		'post_author' => 1,
		'menu_order'  => $order,
	];

	if ( $id ) { $data['ID'] = $id; $id = wp_update_post($data); }
	else { $id = wp_insert_post($data); }

	if ( is_wp_error($id) ) { echo "ERR $name: ".$id->get_error_message()."\n"; continue; }

	if ( ! empty($map[$path]) ) {
		set_post_thumbnail($id, $map[$path]);
	} else {
		echo "no image for $name ($path)\n";
	}

	if ( $link ) {
		update_post_meta($id, 'mfn-post-link', $link);
		update_post_meta($id, 'mfn-post-target', '_blank');
	}

	echo "$order. $name -> #$id (thumb ".get_post_thumbnail_id($id).")\n";
	$order++;
}
