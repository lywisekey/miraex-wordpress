<?php
require __DIR__ . '/bootstrap.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
wp_set_current_user(1);

add_filter('upload_mimes', function($m){ $m['svg'] = 'image/svg+xml'; return $m; });
add_filter('wp_check_filetype_and_ext', function($data, $file, $filename, $mimes){
	if ( preg_match('/\.svg$/i', $filename) ) {
		$data['ext'] = 'svg'; $data['type'] = 'image/svg+xml';
	}
	return $data;
}, 10, 4);

$url = 'https://ashy-forest-0b7587303.7.azurestaticapps.net/assets/img/logos/imd.svg';
$existing = get_posts(['post_type'=>'attachment','numberposts'=>1,'fields'=>'ids','meta_key'=>'_miraex_src_url','meta_value'=>$url]);
if ( $existing ) { echo "exists: {$existing[0]}\n"; exit; }

$tmp = download_url($url);
if ( is_wp_error($tmp) ) { echo "err: ".$tmp->get_error_message()."\n"; exit; }
$id = media_handle_sideload(['name'=>'imd.svg','tmp_name'=>$tmp], 0, 'IMD');
if ( is_wp_error($id) ) { @unlink($tmp); echo "err: ".$id->get_error_message()."\n"; exit; }
update_post_meta($id, '_wp_attachment_image_alt', 'IMD');
update_post_meta($id, '_miraex_src_url', $url);
echo "imported: $id ".wp_get_attachment_url($id)."\n";
