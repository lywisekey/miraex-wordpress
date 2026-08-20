<?php
require __DIR__ . '/bootstrap.php';
$id = (int) get_option('page_on_front');
$items = get_post_meta($id, 'mfn-page-items', true);

echo "front page id: $id (", get_the_title($id), ")\n";
echo "is_array: ", is_array($items) ? 'yes' : 'NO', " sections: ", is_array($items) ? count($items) : 0, "\n";

$bad = 0; $counts = ['section'=>0,'wrap'=>0,'item'=>0];
$types = [];
foreach ($items as $s) {
	$counts['section']++;
	foreach ($s['wraps'] as $w) {
		$counts['wrap']++;
		foreach ($w['items'] as $i) {
			$counts['item']++;
			$types[$i['type']] = ($types[$i['type']] ?? 0) + 1;
			foreach (['type','uid','size','jsclass','attr'] as $k) {
				if (!isset($i[$k])) { $bad++; echo "MISSING $k in ", $i['title'] ?? '?', "\n"; }
			}
		}
	}
}
print_r($counts);
print_r($types);
echo "malformed items: $bad\n";
echo "hide title meta: ", get_post_meta($id,'mfn-post-hide-title',true), "\n";
echo "fonts: ", get_post_meta($id,'mfn-page-fonts',true), "\n";
echo "local style bytes: ", strlen(get_post_meta($id,'mfn-page-local-style',true)), "\n";
echo "seo bytes: ", strlen(get_post_meta($id,'mfn-page-items-seo',true)), "\n";
echo "clients: ", count(get_posts(['post_type'=>'client','numberposts'=>-1])), "\n";
echo "attachments: ", count(get_posts(['post_type'=>'attachment','numberposts'=>-1,'meta_key'=>'_miraex_src_url'])), "\n";
