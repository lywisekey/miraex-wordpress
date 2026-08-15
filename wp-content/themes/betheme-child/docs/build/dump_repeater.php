<?php
require __DIR__ . '/bootstrap.php';
require_once(get_theme_file_path("/functions/builder/class-mfn-builder-fields.php"));
$f = new Mfn_Builder_Fields();
$items = $f->get_items();
foreach (explode(',', $argv[1]) as $w) {
  $w = trim($w);
  if (!isset($items[$w])) { echo "!! $w\n"; continue; }
  echo "=== $w ===\n";
  foreach ($items[$w]['attr'] as $a) {
    if (!is_array($a) || empty($a['id'])) continue;
    if (in_array($a['type'] ?? '', ['tabs','dynamic_items','hotspot','multi_text'])) {
      echo "REPEATER id={$a['id']} type={$a['type']}\n";
      echo json_encode($a, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), "\n";
    }
  }
}
