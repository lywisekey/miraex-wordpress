<?php
require __DIR__ . '/bootstrap.php';
require_once(get_theme_file_path("/functions/builder/class-mfn-builder-fields.php"));
$f = new Mfn_Builder_Fields();
$items = $f->get_items();
$want = explode(',', $argv[1] ?? '');
$filter = $argv[2] ?? '';
function walk($attrs,$filter){
  foreach($attrs as $a){
    if(!is_array($a)) continue;
    if(isset($a['fields'])){ walk($a['fields'],$filter); continue; }
    if(!isset($a['id'])) continue;
    if($filter && strpos($a['id'],$filter)===false) continue;
    echo "- ".$a['id']."\n    sel: ".($a['selector']??'-')."\n    style: ".($a['style']??'-')." | type=".($a['type']??'')."\n";
  }
}
foreach($want as $w){
  $w=trim($w); if(!$w) continue;
  if(!isset($items[$w])){ echo "!! $w\n"; continue; }
  echo "=== $w ===\n"; walk($items[$w]['attr'],$filter);
}
