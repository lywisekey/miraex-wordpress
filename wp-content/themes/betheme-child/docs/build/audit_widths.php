<?php
/**
 * Audit every wrap/item that carries a custom width and report whether the
 * matching switch is set, so the BeBuilder UI never disagrees with the CSS.
 */
require __DIR__ . '/bootstrap.php';

$bad = 0;

foreach ( get_posts([ 'post_type' => ['page','template'], 'numberposts' => -1, 'post_status' => 'publish' ]) as $p ) {
	$items = get_post_meta( $p->ID, 'mfn-page-items', true );
	if ( ! is_array( $items ) ) { continue; }

	foreach ( $items as $sec ) {
		foreach ( $sec['wraps'] ?? [] as $w ) {
			$has_w = isset( $w['attr']['css_advanced_flex'] );
			$sw    = $w['attr']['width_switcher'] ?? '';
			if ( $has_w ) {
				$val = $w['attr']['css_advanced_flex']['val']['desktop'] ?? '?';
				$ok  = ( 'custom' === $sw );
				printf( "%-9s %-28s wrap %-16s width=%-8s switcher=%-8s %s\n",
					$p->post_type, $p->post_name, $w['title'], $val, $sw ?: "''", $ok ? 'ok' : '<<< MISMATCH' );
				if ( ! $ok ) { $bad++; }
			} elseif ( 'custom' === $sw ) {
				printf( "%-9s %-28s wrap %-16s switcher=custom but no width <<< MISMATCH\n", $p->post_type, $p->post_name, $w['title'] );
				$bad++;
			}

			foreach ( $w['items'] ?? [] as $it ) {
				$isw = $it['attr']['width_switcher'] ?? '';
				if ( 'custom' === $isw && ! isset( $it['attr']['css_advanced_flex'] ) ) {
					printf( "%-9s %-28s item %-16s switcher=custom but no width <<< MISMATCH\n", $p->post_type, $p->post_name, $it['title'] );
					$bad++;
				}
			}
		}
	}
}

echo "\nmismatches: $bad\n";
