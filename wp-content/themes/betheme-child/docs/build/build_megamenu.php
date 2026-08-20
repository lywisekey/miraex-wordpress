<?php
/**
 * Build the "Solutions" mega menu template, matching `.mega` in
 * html-redesign/css/app.css:
 *
 *   .mega{width:min(620px,78vw);background:rgba(11,28,52,.97);backdrop-filter:blur(18px);
 *         border:1px solid var(--line);border-radius:18px;padding:16px;box-shadow:var(--shadow);
 *         display:grid;grid-template-columns:1fr 1fr;gap:6px}
 *   .mega a{display:flex;gap:13px;padding:13px;border-radius:12px}
 *   .mega a:hover{background:rgba(25,198,218,.08)}
 *   .mega a .ic{38px;radius:10px;background:var(--grad-spectral-soft);border:1px solid var(--line)}
 *   .mega a b{color:#fff;font-size:14.5px;font-weight:600;margin-bottom:2px}
 *   .mega a span{color:var(--slate);font-size:12.5px;line-height:1.4}
 *
 * The panel frame itself is not part of the builder content — BeTheme wraps the
 * template in `#mfn-megamenu-<id>` and styles it from the template's own option
 * metas (`mfn-page-options-style`), so both are written here.
 *
 * Attaching it to a menu item is a separate step:
 *   Appearance → Menus → open the "Solutions" item → "Mega menu" → pick this template.
 *
 * Idempotent.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';

/* ------------------------------------------------------------------ items */

$links = [
	[
		'key'  => 'mm-dqc',
		'icon' => 'fas fa-microchip',
		'title' => 'Distributed Quantum Computing',
		'desc' => 'Interconnect QPUs beyond a single cryostat',
		'link' => '/distributed-quantum-computing/',
	],
	[
		'key'  => 'mm-sensing',
		'icon' => 'fas fa-satellite-dish',
		'title' => 'Quantum Sensing',
		'desc' => 'RF photonics for precision &amp; defence',
		'link' => '/quantum-sensing/',
	],
	[
		'key'  => 'mm-networking',
		'icon' => 'fas fa-project-diagram',
		'title' => 'Quantum Networking',
		'desc' => 'Repeaters &amp; QKD toward the quantum internet',
		'link' => '/quantum-networking/',
	],
	[
		'key'  => 'mm-tflt',
		'icon' => 'fas fa-layer-group',
		'title' => 'TFLT PIC Platform',
		'desc' => 'The technology beneath every vertical',
		'link' => '/technology/',
	],
];

$wraps = [];

foreach ( $links as $l ) {
	$wraps[] = wrap( $l['key'] . '-w', $l['title'], '1/2', [
		/* .mega{gap:6px} — 3px each side of the outer wrap */
		'css_advanced_gutter' => css( WRP, 'padding', [ 'desktop' => dim( '0px', '3px', '6px', '3px' ) ] ),
	], [
		item( $l['key'], 'icon_box', $l['title'], '1/1', [
			'title'         => $l['title'],
			'title_tag'     => 'h4',
			'content'       => $l['desc'],
			'icon'          => $l['icon'],
			'icon_position' => 'left',
			'link'          => $l['link'],
			'border'        => 0,

			/* .mega a{padding:13px;border-radius:12px} + hover */
			'css_advanced_margin'           => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
			'css_advanced_padding'          => css( ITEM . ' .mcb-column-inner', 'padding', [ 'desktop' => dim( '13px', '13px', '13px', '13px' ) ] ),
			'css_advanced_border_radius'    => css( ITEM . ' .mcb-column-inner', 'border-radius', [ 'desktop' => '12px 12px 12px 12px' ] ),
			'css_advanced_transition'       => css( ITEM . ' .mcb-column-inner', 'transition', '0.2s' ),
			'css_advanced_background_hover' => css( ITEM . ' .mcb-column-inner:hover', 'background-color', 'rgba(25,198,218,0.08)' ),

			/* BeTheme sizes .icon_position_left for a 126px round icon */
			'css_icon_box_left_padding'   => css( ITEM . ' .icon_box.icon_position_left', 'padding-left', '51px' ),
			'css_icon_box_pad_v'          => css( ITEM . ' .icon_box', 'padding-top', '0px' ),
			'css_icon_box_pad_v2'         => css( ITEM . ' .icon_box', 'padding-bottom', '0px' ),
			'css_icon_box_left_minheight' => css( ITEM . ' .icon_box.icon_position_left', 'min-height', '0px' ),
			'css_icon_box_left_desc_pad'  => css( ITEM . ' .icon_box.icon_position_left .desc_wrapper', 'padding-top', '0px' ),
			'css_icon_box_left_icon_top'  => css( ITEM . ' .icon_box.icon_position_left .icon_wrapper', 'top', '0px' ),

			/* .mega a .ic — 38px tile with the soft spectral gradient */
			'css_icon_boxicon_wrapper_shadow'   => css( ITEM . ' .icon_box .icon_wrapper', 'box-shadow', 'none' ),
			'css_icon_boxicon_wrapper_bgimage'  => css( ITEM . ' .icon_box .icon_wrapper', 'background-image', 'none' ),
			'css_icon_boxicon_wrapper_boxsizing'=> css( ITEM . ' .icon_box .icon_wrapper', 'box-sizing', 'border-box' ),
			'css_icon_boxicon_wrapper_background'    => css( ITEM . ' .icon_box .icon_wrapper', 'background', 'linear-gradient(100deg,rgba(25,198,218,0.16),rgba(124,108,240,0.16))' ),
			'css_icon_boxicon_wrapper_border_style'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-style', 'solid' ),
			'css_icon_boxicon_wrapper_border_color'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-color', $C['line'] ),
			'css_icon_boxicon_wrapper_border_width'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
			'css_icon_boxicon_wrapper_border_radius' => css( ITEM . ' .icon_box .icon_wrapper', 'border-radius', [ 'desktop' => '10px 10px 10px 10px' ] ),
			'css_icon_boxicon_wrapper_width'         => css( ITEM . ' .icon_box .icon_wrapper', 'width', '38px' ),
			'css_icon_boxicon_wrapper_height'        => css( ITEM . ' .icon_box .icon_wrapper', 'height', '38px' ),
			'css_icon_boxicon_wrapper_lineheight'    => css( ITEM . ' .icon_box .icon_wrapper', 'line-height', '1' ),
			'css_icon_boxicon_wrapper_fontsize'    => css( ITEM . ' .icon_box .icon_wrapper', 'font-size', '20px' ),
			'css_icon_boxicon_wrapper_margin'        => css( ITEM . ' .icon_box .icon_wrapper', 'margin', [ 'desktop' => dim( '0px', '13px', '0px', '0px' ) ] ),
			/* BeTheme defaults the reference does not have:
			     .icon_box:hover .icon_wrapper:before{background-color:#0089F7}  (theme accent)
			     .icon_box:hover .icon_wrapper{transform:scale(.94)}
			     .icon_box:hover .icon_wrapper i{top:-5px}
			     .icon_box:hover .desc_wrapper .title:before{width:100px} */
			'css_icon_hover_no_fill'   => css( ITEM . ' .icon_box:hover .icon_wrapper:before,' . ITEM . ' .icon_box a:hover .icon_wrapper:before', 'background-color', 'transparent' ),
			'css_icon_hover_no_scale'  => css( ITEM . ' .icon_box:hover .icon_wrapper', 'transform', 'none' ),
			'css_icon_hover_no_jump'   => css( ITEM . ' .icon_box:hover .icon_wrapper i', 'top', '0px' ),
			'css_icon_hover_no_rule'   => css( ITEM . ' .icon_box:hover .desc_wrapper .title:before', 'width', '0px' ),
			'css_icon_before_radius'   => css( ITEM . ' .icon_box .icon_wrapper:before', 'border-radius', '10px' ),
			'css_icon_before_border'   => css( ITEM . ' .icon_box .icon_wrapper:before', 'border-color', 'transparent' ),
			'css_icon_boxicon_wrappericon_color'     => css( ITEM . ' .icon_box .icon_wrapper .icon', 'color', $C['cyan'] ),
			'css_icon_boxicon_wrapperi_font_size'    => css( ITEM . ' .icon_box .icon_wrapper i', 'font-size', [ 'desktop' => '20px' ] ),

			/* .mega a b / .mega a span */
			'css_icon_boxdesc_wrappertitle_color'      => css( ITEM . ' .icon_box .desc_wrapper .title', 'color', $C['white'] ),
			'css_icon_boxdesc_wrappertitle_typography' => css( ITEM . ' .icon_box .desc_wrapper .title', 'typography', [
				'desktop'     => [ 'font-size' => '14.5px', 'line-height' => '1.25', 'font-family' => $F_TEXT ],
				'font-weight' => '600',
			] ),
			'css_icon_boxdesc_wrappertitle_margin'    => css( ITEM . ' .icon_box .desc_wrapper .title', 'margin', [ 'desktop' => dim( '0px', '', '2px', '' ) ] ),
			'css_icon_boxdesc_wrapperdesc_color'      => css( ITEM . ' .icon_box .desc_wrapper .desc', 'color', $C['slate'] ),
			'css_icon_boxdesc_wrapperdesc_typography' => css( ITEM . ' .icon_box .desc_wrapper .desc', 'typography', [
				'desktop' => [ 'font-size' => '12.5px', 'line-height' => '1.4', 'font-family' => $F_TEXT ],
			] ),
			'css_icon_boxdesc_wrapperdesc_margin'     => css( ITEM . ' .icon_box .desc_wrapper .desc', 'margin', [ 'desktop' => dim( '0px', '', '0px', '' ) ] ),
		] ),
	], '1/2', '1/1' );
}

$sections = [
	section( 'mm-section', 'Solutions mega menu', [
		/* the panel's own padding comes from the template options */
		'css_advanced_padding'   => css( SEC, 'padding', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
		'css_advanced_max_width'  => css( SEC . ' .section_wrapper', 'max-width', '100%' ),
		'css_advanced_wrap_width' => css( SEC . ' .section_wrapper', 'width', '100%' ),
		'css_advanced_wrap_pad'   => css( SEC . ' .section_wrapper', 'padding', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
		'css_advanced_sec_pad'    => css( SEC . '.mcb-section', 'padding', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
		/* the reference is a CSS grid, so both cells in a row share a height */
		'css_advanced_align_items' => css( SECW, 'align-items', [ 'desktop' => 'stretch' ] ),
	], $wraps ),
];

/* ------------------------------------------------------------- persistence */

$existing = get_posts([
	'post_type'   => 'template',
	'numberposts' => 1,
	'meta_key'    => 'mfn_template_type',
	'meta_value'  => 'megamenu',
	'post_status' => 'any',
]);

if ( $existing ) {
	$tmpl_id = $existing[0]->ID;
	wp_update_post([ 'ID' => $tmpl_id, 'post_title' => 'Mega menu — Solutions', 'post_status' => 'publish' ]);
} else {
	$tmpl_id = wp_insert_post([
		'post_type'   => 'template',
		'post_title'  => 'Mega menu — Solutions',
		'post_name'   => 'megamenu-solutions',
		'post_status' => 'publish',
		'post_author' => 1,
	]);
	update_post_meta( $tmpl_id, 'mfn_template_type', 'megamenu' );
}

$nodes = mfn_store_template( $tmpl_id, $sections );

/* panel geometry: .mega{width:min(620px,78vw)}, anchored to the menu item */
update_post_meta( $tmpl_id, 'megamenu_width', 'custom-width' );
update_post_meta( $tmpl_id, 'megamenu_custom_width', '620px' );
update_post_meta( $tmpl_id, 'megamenu_custom_position', 'left' );

/* panel design — shown in Mega menu Options … */
update_post_meta( $tmpl_id, 'css_padding', '16px' );
update_post_meta( $tmpl_id, 'css_bg', 'rgba(11,28,52,0.97)' );
update_post_meta( $tmpl_id, 'css_border_style', 'solid' );
update_post_meta( $tmpl_id, 'css_border_color', 'rgba(255,255,255,0.10)' );
update_post_meta( $tmpl_id, 'css_border_width', '1px 1px 1px 1px' );
update_post_meta( $tmpl_id, 'css_border_radius', '18px 18px 18px 18px' );

/* … and rendered from this map by MfnMegaMenu::css(); 'postid' is swapped for the
   template id at render time. backdrop-filter / box-shadow have no option field,
   so they are written straight into the map. */
update_post_meta( $tmpl_id, 'mfn-page-options-style', [
	'#mfn-megamenu-postid' => [
		'padding'          => '16px',
		'background-color' => 'rgba(11,28,52,0.97)',
		'border-style'     => 'solid',
		'border-color'     => 'rgba(255,255,255,0.10)',
		'border-width'     => '1px 1px 1px 1px',
		'border-radius'    => '18px 18px 18px 18px',
		'backdrop-filter'  => 'blur(18px)',
		'box-shadow'       => '0 18px 50px -20px rgba(0,0,0,0.7)',
	],
	/* BeTheme wraps megamenu builder content in .mfn-megamenu-tmpl-builder with
	   24px padding + max-width:1200px; the panel owns its padding instead. */
	'#mfn-megamenu-postid .mfn-megamenu-tmpl-builder' => [
		'padding'   => '0',
		'max-width' => 'none',
	],
] );

printf( "megamenu template=%d nodes=%d width=620px\n", $tmpl_id, $nodes );

/* report which menu items could host it */
$menu = wp_get_nav_menu_object( 'Miraex Main Menu' );
if ( $menu ) {
	foreach ( wp_get_nav_menu_items( $menu->term_id ) as $mi ) {
		if ( 0 === (int) $mi->menu_item_parent ) {
			$attached = get_post_meta( $mi->ID, 'mfn_menu_item_megamenu', true );
			printf( "  menu item #%d %-12s %s\n", $mi->ID, $mi->title, $attached ? "→ mega menu #$attached" : '' );
		}
	}
}
