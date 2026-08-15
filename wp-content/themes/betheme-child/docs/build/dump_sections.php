<?php
require __DIR__ . '/bootstrap.php';
$pages = get_posts([ 'post_type' => 'page', 'numberposts' => -1, 'post_status' => 'any', 'orderby' => 'ID', 'order' => 'ASC' ]);
foreach ( $pages as $pg ) {
	$items = get_post_meta( $pg->ID, 'mfn-page-items', true );
	if ( ! is_array( $items ) || ! $items ) { continue; }
	printf( "\n== #%d %s (/%s/)\n", $pg->ID, $pg->post_title, $pg->post_name );
	foreach ( $items as $s ) {
		$title = $s['title'] ?? ( $s['attr']['title'] ?? '(untitled)' );
		$wraps = isset( $s['wraps'] ) ? count( $s['wraps'] ) : 0;
		$items_n = 0; $types = [];
		foreach ( ( $s['wraps'] ?? [] ) as $w ) {
			foreach ( ( $w['items'] ?? [] ) as $i ) { $items_n++; $types[ $i['type'] ] = 1; }
		}
		printf( "   %-46s wraps=%-2d items=%-3d [%s]\n", $title, $wraps, $items_n, implode( ',', array_keys( $types ) ) );
	}
}
