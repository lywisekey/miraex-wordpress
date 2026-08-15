<?php
require __DIR__ . '/bootstrap.php';

$rows = [];
$types = [];

foreach ( get_posts([ 'post_type' => ['page','template'], 'numberposts' => -1, 'post_status' => 'publish' ]) as $p ) {
	$items = get_post_meta( $p->ID, 'mfn-page-items', true );
	if ( ! is_array( $items ) ) { continue; }

	$s = $w = $i = 0;
	foreach ( $items as $sec ) {
		$s++;
		foreach ( $sec['wraps'] ?? [] as $wr ) {
			$w++;
			foreach ( $wr['items'] ?? [] as $it ) {
				$i++;
				$types[ $it['type'] ] = ( $types[ $it['type'] ] ?? 0 ) + 1;
			}
		}
	}

	$css = wp_upload_dir()['basedir'] . '/betheme/css/post-' . $p->ID . '.css';

	$rows[] = sprintf( '%-4d %-9s %-34s %2d/%3d/%3d  css=%6s  %s',
		$p->ID, $p->post_type, $p->post_name, $s, $w, $i,
		file_exists( $css ) ? size_format( filesize( $css ) ) : 'MISSING',
		get_permalink( $p->ID ) );
}

echo "ID   type      slug                               sec/wrap/item  css      url\n";
echo implode( "\n", $rows ), "\n\n";
echo 'element types used: '; ksort( $types );
foreach ( $types as $t => $n ) { echo "$t($n) "; }
echo "\n\nmedia: ", count( get_posts(['post_type'=>'attachment','numberposts'=>-1,'meta_key'=>'_miraex_src_url']) ),
	" · clients: ", count( get_posts(['post_type'=>'client','numberposts'=>-1]) ),
	" · menu items: ", count( wp_get_nav_menu_items( 'Miraex Main Menu' ) ?: [] ), "\n";
echo "header tmpl: ", get_option('mfn_header_entire_site'), " · footer tmpl: ", get_option('mfn_footer_entire_site'),
	" · front page: ", get_option('page_on_front'), "\n";
