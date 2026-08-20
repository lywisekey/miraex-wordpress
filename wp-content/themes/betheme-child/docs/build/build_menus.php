<?php
/**
 * Create the navigation menu used by the header template and the footer link lists.
 *
 * Idempotent: matched by menu name, items matched by title.
 */

require __DIR__ . '/bootstrap.php';
/**
 * Menu tree taken from html-redesign/index.html (primary nav + mobile nav).
 * Children of "Solutions" mirror the mega-menu entries.
 */
$tree = [
	[
		'title'    => 'Solutions',
		'url'      => '#',
		'children' => [
			[ 'title' => 'Distributed Quantum Computing', 'url' => '/distributed-quantum-computing/', 'desc' => 'Interconnect QPUs beyond a single cryostat' ],
			[ 'title' => 'Quantum Sensing',               'url' => '/quantum-sensing/',               'desc' => 'RF photonics for precision & defence' ],
			[ 'title' => 'Quantum Networking',            'url' => '/quantum-networking/',            'desc' => 'Repeaters & QKD toward the quantum internet' ],
			[ 'title' => 'TFLT PIC Platform',             'url' => '/technology/',                    'desc' => 'The technology beneath every vertical' ],
		],
	],
	[ 'title' => 'Technology', 'url' => '/technology/' ],
	[ 'title' => 'Company',    'url' => '/about/' ],
	[ 'title' => 'News',       'url' => '/news/' ],
	[ 'title' => 'Resources',  'url' => '/resources/' ],
	[ 'title' => 'Careers',    'url' => '/careers/' ],
];

$menu_name = 'Miraex Main Menu';
$menu      = wp_get_nav_menu_object( $menu_name );

if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		fwrite( STDERR, $menu_id->get_error_message() . "\n" );
		exit( 1 );
	}
} else {
	$menu_id = $menu->term_id;
}

/* index existing items by title so re-runs update instead of duplicating */
$existing = [];
foreach ( wp_get_nav_menu_items( $menu_id ) ?: [] as $it ) {
	$existing[ $it->title ] = $it->ID;
}

$order = 1;

foreach ( $tree as $node ) {
	$parent_id = wp_update_nav_menu_item( $menu_id, $existing[ $node['title'] ] ?? 0, [
		'menu-item-title'     => $node['title'],
		'menu-item-url'       => $node['url'],
		'menu-item-status'    => 'publish',
		'menu-item-type'      => 'custom',
		'menu-item-position'  => $order++,
		'menu-item-parent-id' => 0,
	] );

	echo "· {$node['title']} -> #{$parent_id}\n";

	foreach ( $node['children'] ?? [] as $child ) {
		$child_id = wp_update_nav_menu_item( $menu_id, $existing[ $child['title'] ] ?? 0, [
			'menu-item-title'       => $child['title'],
			'menu-item-url'         => $child['url'],
			'menu-item-description' => $child['desc'] ?? '',
			'menu-item-status'      => 'publish',
			'menu-item-type'        => 'custom',
			'menu-item-position'    => $order++,
			'menu-item-parent-id'   => $parent_id,
		] );
		echo "    └ {$child['title']} -> #{$child_id}\n";
	}
}

echo "menu_id={$menu_id}\n";
