<?php
/**
 * Rebuild the "Header Miraex" BeBuilder header template to match
 * html-redesign/index.html  (.site-header + .nav + .mega + .nav-cta).
 *
 * Three header versions, as BeTheme expects:
 *   ver 'default'        → transparent bar sitting over the hero
 *   ver 'header-sticky'  → dark translucent bar shown once the page scrolls
 *   ver 'header-mobile'  → logo + burger under 768px
 *
 * Idempotent.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';

/* ------------------------------------------------------------- pre-flight */

$menu = wp_get_nav_menu_object( 'Miraex Main Menu' );
if ( ! $menu ) {
	fwrite( STDERR, "Run build_menus.php first — nav menu 'Miraex Main Menu' is missing.\n" );
	exit( 1 );
}
$menu_id = (string) $menu->term_id;

$logo = media( 'assets/img/miraex-logo-full.png' );

/* -------------------------------------------------------------- elements */

/** Logo, scaled to the reference's 32px bar logo. */
function hdr_logo( $key ) {
	global $logo;

	return item( $key, 'header_logo', 'Miraex Logo', '1/1', [
		'image'          => $logo['url'],
		'link'           => '/',
		'width_switcher' => 'inline',
		'css_logo_align' => css( ITEM . ' .logo-wrapper', 'align-items', 'center' ),
		'css_logo_img_height' => css( ITEM . ' .logo-wrapper img', 'height', [ 'desktop' => '32px', 'mobile' => '26px' ] ),
		'css_logo_img_width'  => css( ITEM . ' .logo-wrapper img', 'width', 'auto' ),
	] );
}

/** Primary menu, dark styling + dark dropdown for the Solutions submenu. */
function hdr_menu( $key ) {
	global $C, $F_TEXT, $menu_id;

	return item( $key, 'header_menu', 'Miraex Main Menu', '1/1', [
		'menu_display'         => $menu_id,
		'separator'            => 'off',
		'submenu_display'      => 'hover',
		'submenu_icon_display' => 'on',
		'submenu_icon'         => 'icon-down-dir',
		'submenu_subicon'      => 'fas fa-arrow-right',
		'submenu_animation'    => 'fade-up',
		'animation'            => 'text-bg-line',
		'icon_align'           => 'left',
		'width_switcher'       => 'inline',
		'visibility'           => ' hide-tablet hide-mobile',

		'css_header_menu_justify' => css( ITEM . ' .mfn-header-menu', 'justify-content', [ 'desktop' => 'flex-end' ] ),

		/* top level */
		'css_menu-link_typography' => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li > a.mfn-menu-link', 'typography', [
			'desktop'     => [ 'font-size' => '14.5px', 'font-family' => $F_TEXT ],
			'font-weight' => '500',
		] ),
		'css_menu-link_padding' => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li > a.mfn-menu-link', 'padding', [ 'desktop' => dim( '10px', '14px', '10px', '14px' ) ] ),
		'css_menu-link_color'   => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li > a.mfn-menu-link', 'color', $C['inkSoft'] ),
		'css_menu-link_color_hover' => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li:hover > a.mfn-menu-link', 'color', $C['white'] ),
		'css_menu-lihovera-menu-link_background_color_hover' => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li:hover > a.mfn-menu-link', 'background-color', 'rgba(255,255,255,0.05)' ),
		'css_menu-link_border_radius' => css( ITEM . ' .mfn-header-menu > li.mfn-menu-li > a.mfn-menu-link', 'border-radius', [ 'desktop' => '10px 10px 10px 10px' ] ),

		/* submenu panel */
		'css_submenu_background_color' => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'background-color', 'rgba(11,28,52,0.97)' ),
		'css_submenu_border_style'     => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'border-style', 'solid' ),
		'css_submenu_border_color'     => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'border-color', $C['line'] ),
		'css_submenu_border_width'     => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_submenu_border_radius'    => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'border-radius', [ 'desktop' => '18px 18px 18px 18px' ] ),
		'css_submenu_padding'          => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'padding', [ 'desktop' => dim( '12px', '12px', '12px', '12px' ) ] ),
		'css_submenu_min_width'        => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu', 'min-width', '320px' ),

		/* submenu links */
		'css_menu-liul-submenu_menu-link_typography' => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu li.mfn-menu-li a.mfn-menu-link', 'typography', [
			'desktop'     => [ 'font-size' => '14.5px', 'font-family' => $F_TEXT ],
			'font-weight' => '600',
		] ),
		'css_submenu_link_color'        => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu li.mfn-menu-li a.mfn-menu-link', 'color', $C['white'] ),
		'css_submenu_link_bg_hover'     => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu li.mfn-menu-li:hover > a.mfn-menu-link', 'background-color', 'rgba(25,198,218,0.08)' ),
		'css_submenu_link_radius'       => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu li.mfn-menu-li a.mfn-menu-link', 'border-radius', [ 'desktop' => '12px 12px 12px 12px' ] ),
		'css_submenu_link_padding'      => css( ITEM . ' .mfn-header-menu li.mfn-menu-li ul.mfn-submenu li.mfn-menu-li a.mfn-menu-link', 'padding', [ 'desktop' => dim( '11px', '13px', '11px', '13px' ) ] ),
		/* the mega-menu style description under each submenu label */
		'css_submenu_desc_color'        => css( ITEM . ' .mfn-header-menu ul.mfn-submenu .menu-desc', 'color', $C['slate'] ),
		'css_submenu_desc_typography'   => css( ITEM . ' .mfn-header-menu ul.mfn-submenu .menu-desc', 'typography', [
			'desktop'     => [ 'font-size' => '12.5px', 'line-height' => '1.4', 'font-family' => $F_TEXT ],
			'font-weight' => '400',
		] ),
	] );
}

/** "Talk to us" ghost button. */
function hdr_cta( $key, $visibility = '' ) {
	$b = button_item( $key, 'Talk to us', '/contact/', 'ghost', '1/1', 'right' );

	/* .site-header .bar{height:74px} holds a plain .btn — same 14px/26px as everywhere
	   else, so the header button keeps button_item()'s defaults. */
	unset( $b['attr']['icon'], $b['attr']['icon_position'] );
	$b['attr']['visibility'] = $visibility;
	/* button_item() zeroes the column margins so a .btn-row gap stays exactly 14px; the
	   header bar has a single button, which has to end on the content edge instead. */
	$b['attr']['css_advanced_margin'] = css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '12px', '0px', '0px' ) ] );

	return $b;
}

/** Burger for the off-canvas mobile menu. */
function hdr_burger( $key ) {
	global $C, $F_DISPLAY, $F_TEXT, $menu_id;

	return item( $key, 'header_burger', 'Mobile Menu', '1/1', [
		'icon'                 => 'fas fa-bars',
		'menu_display'         => $menu_id,
		'menu_pos'             => 'right',
		'submenu_icon_display' => 'on',
		'submenu_icon'         => 'icon-down-dir',
		'submenu_subicon'      => 'fas fa-arrow-right',
		'submenu_animation'    => 'fade-up',
		'icon_position'        => [ 'desktop' => 'top' ],
		'icon_align'           => 'left',
		'width_switcher'       => 'inline',
		'visibility'           => ' hide-desktop hide-laptop',

		'css_icon-wrapperi_color'          => css( ITEM . ' .icon-wrapper i', 'color', $C['white'] ),
		'css_icon-boxicon_icon_size'       => css( ITEM . ' .mfn-icon-box .icon-wrapper', '--mfn-header-menu-icon-size', [ 'desktop' => '20px' ] ),
		'css_menu-burger_padding'          => css( ITEM . ' .mfn-header-menu-burger', 'padding', [ 'desktop' => dim( '11px', '11px', '11px', '11px' ) ] ),
		'css_menu-burger_border_style'     => css( ITEM . ' .mfn-header-menu-burger', 'border-style', 'solid' ),
		'css_menu-burger_border_color'     => css( ITEM . ' .mfn-header-menu-burger', 'border-color', $C['line'] ),
		'css_menu-burger_border_width'     => css( ITEM . ' .mfn-header-menu-burger', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_menu-burger_border_radius'    => css( ITEM . ' .mfn-header-menu-burger', 'border-radius', [ 'desktop' => '10px 10px 10px 10px' ] ),

		/* off-canvas panel */
		'css_menu-sidebar_background_color' => css( ITEM . ' .mfn-header-tmpl-menu-sidebar', 'background-color', $C['navy900'] ),
		'css_menu-sidebar_header_menu_sidebar_width' => css( ITEM . ' .mfn-header-tmpl-menu-sidebar', '--mfn-header-menu-sidebar-width', [ 'desktop' => '320px' ] ),
		'css_menu-sidebar-wrapper_padding'  => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-tmpl-menu-sidebar-wrapper', 'padding', [ 'desktop' => dim( '80px', '28px', '28px', '28px' ) ] ),
		/* BeTheme puts the close button at 10px, and pushes it to 42px/56px whenever the
		   WP admin bar is on screen — pin it so it sits in the same place for everyone. */
		'css_menu-sidebar_close_top'        => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-close-icon', 'top', '16px' ),
		'css_menu-sidebar_close_right'      => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-close-icon', 'right', '16px' ),
		'css_menu_lia_color'                => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-menu > li > a', 'color', $C['ink'] ),
		'css_menu_lia_color_hover'          => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-menu > li:hover > a', 'color', $C['cyan'] ),
		'css_menu_lia_typography'           => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-menu > li > a', 'typography', [
			'desktop'     => [ 'font-size' => '17px', 'font-family' => $F_DISPLAY ],
			'font-weight' => '500',
		] ),
		'css_menu_li-submenulia_color'      => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-menu li ul li a', 'color', $C['inkSoft'] ),
		'css_menu_li-submenu_typography'    => css( ITEM . ' .mfn-header-tmpl-menu-sidebar .mfn-header-menu li ul li a', 'typography', [
			'desktop' => [ 'font-size' => '14.5px', 'font-family' => $F_TEXT ],
		] ),
	] );
}

/* -------------------------------------------------------------- sections */

/**
 * One header bar. $variant drives uid namespace, background and BeTheme's `ver`.
 */
function hdr_bar( $variant, $ver, array $section_attr, $mobile = false ) {
	$k = 'hdr-' . $variant;

	if ( $mobile ) {
		$wraps = [
			wrap( $k . '-w-logo', 'Logo', '1/2', [
				'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
				'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-start' ] ),
			], [ hdr_logo( $k . '-logo' ) ], '1/2', '1/2' ),
			wrap( $k . '-w-nav', 'Navigation', '1/2', [
				'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
				'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-end' ] ),
			], [ hdr_burger( $k . '-burger' ) ], '1/2', '1/2' ),
		];
	} else {
		$wraps = [
			wrap( $k . '-w-logo', 'Logo', '1/4', [
				'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
				'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-start' ] ),
			], [ hdr_logo( $k . '-logo' ) ], '1/3', '1/2' ),
			wrap( $k . '-w-nav', 'Navigation', '1/2', [
				'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
				'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-end' ] ),
			], [ hdr_menu( $k . '-menu' ) ], '1/3', '1/2' ),
			wrap( $k . '-w-cta', 'Call to action', '1/4', [
				'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
				'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-end' ] ),
				'css_advanced_flex_wrap'       => css( WRPI, 'flex-wrap', [ 'desktop' => 'nowrap' ] ),
			], [
				hdr_cta( $k . '-cta', ' hide-tablet hide-mobile' ),
				hdr_burger( $k . '-burger' ),
			], '1/3', '1/2' ),
		];
	}

	$base = [
		'css_advanced_align_items' => css( SECW, 'align-items', [ 'desktop' => 'center', 'tablet' => 'center', 'mobile' => 'center' ] ),
		/* .site-header .bar{justify-content:space-between} — on phones the wraps shrink
		   to their content, so without this the burger sits next to the logo. */
		'css_advanced_justify'     => css( SECW, 'justify-content', [ 'desktop' => 'space-between', 'tablet' => 'space-between', 'mobile' => 'space-between' ] ),
		/* BeTheme rewrites a literal `.mcb-column-inner` in a custom selector into the
		   section-uid'd class, which matches nothing — so the side gutter is removed by
		   redefining the variable the margin is built from (set on body / .mfn-header-tmpl). */
		/* .mfn-header-tmpl sets the column gutter to 5px, which puts the bar 7px inside
		   the page content edge; 12px matches the rest of the site. Zero on phones, where
		   the 15px gutter is the section's padding. */
		'css_hdr_col_gutter_l'            => css( SEC, '--mfn-column-gap-left',  [ 'desktop' => '12px', 'tablet' => '12px', 'mobile' => '0px' ] ),
		'css_hdr_col_gutter_r'            => css( SEC, '--mfn-column-gap-right', [ 'desktop' => '12px', 'tablet' => '12px', 'mobile' => '0px' ] ),
		/* .site-header .bar{height:74px} at every width — a fixed row rather than
		   padding around the tallest item, so the bar cannot grow with the button. */
		'css_advanced_min_height'  => css( SECW, 'min-height', '74px' ),
	];

	$section = section( $k, 'Miraex Header — ' . $variant, array_merge( $base, $section_attr ), $wraps );
	$section['ver'] = $ver;

	return $section;
}

$C_line_soft = 'rgba(255,255,255,0.06)';

$sections = [
	/* transparent bar over the hero */
	hdr_bar( 'default', 'default', [
		'css_advanced_padding' => css( SEC, 'padding', [
			'desktop' => dim( '0px', '', '0px', '' ),
			'mobile'  => dim( '0px', '15px', '0px', '15px' ),
		] ),
	] ),

	/* scrolled: dark translucent + blur, like .site-header.scrolled */
	hdr_bar( 'sticky', 'header-sticky', [
		'css_advanced_padding' => css( SEC, 'padding', [
			'desktop' => dim( '0px', '', '0px', '' ),
			'mobile'  => dim( '0px', '15px', '0px', '15px' ),
		] ),
		'css_advanced_background_color' => css( SEC, 'background-color', 'rgba(8,17,30,0.80)' ),
		'css_advanced_backdrop_filter'  => css( SEC, 'backdrop-filter', 'saturate(150%) blur(14px)' ),
		'css_advanced_border_style'     => css( SEC, 'border-style', 'solid' ),
		'css_advanced_border_color'     => css( SEC, 'border-color', $C_line_soft ),
		'css_advanced_border_width'     => css( SEC, 'border-width', [ 'desktop' => '0 0 1px 0' ] ),
	] ),

	/* under 768px: logo + burger */
	hdr_bar( 'mobile', 'header-mobile', [
		'css_advanced_padding' => css( SEC, 'padding', [
			'desktop' => dim( '0px', '15px', '0px', '15px' ),
		] ),
		'css_advanced_background_color' => css( SEC, 'background-color', 'rgba(8,17,30,0.80)' ),
		'css_advanced_backdrop_filter'  => css( SEC, 'backdrop-filter', 'saturate(150%) blur(14px)' ),
	], true ),
];

/* ---------------------------------------------------------- persistence  */

$existing = get_posts([
	'post_type'   => 'template',
	'numberposts' => 1,
	'meta_key'    => 'mfn_template_type',
	'meta_value'  => 'header',
	'post_status' => 'any',
]);

if ( $existing ) {
	$tmpl_id = $existing[0]->ID;
	wp_update_post([ 'ID' => $tmpl_id, 'post_title' => 'Header Miraex', 'post_status' => 'publish' ]);
} else {
	$tmpl_id = wp_insert_post([
		'post_type'   => 'template',
		'post_title'  => 'Header Miraex',
		'post_name'   => 'header-miraex',
		'post_status' => 'publish',
		'post_author' => 1,
	]);
	update_post_meta( $tmpl_id, 'mfn_template_type', 'header' );
}

mfn_store_template( $tmpl_id, $sections );

/* header behaviour: fixed + overlaying the hero, with sticky and mobile versions */
update_post_meta( $tmpl_id, 'header_position', 'fixed' );
update_post_meta( $tmpl_id, 'body_offset_header', '' );
update_post_meta( $tmpl_id, 'header_width', '' );
update_post_meta( $tmpl_id, 'header_sticky', 'enabled' );
update_post_meta( $tmpl_id, 'header_sticky_width', '' );
update_post_meta( $tmpl_id, 'header_mobile', 'enabled' );
update_post_meta( $tmpl_id, 'mobile_header_position', 'fixed' );
update_post_meta( $tmpl_id, 'mobile_body_offset_header', '' );
update_post_meta( $tmpl_id, 'mfn_template_conditions', wp_json_encode([
	[ 'rule' => 'include', 'var' => 'everywhere', 'archives' => '', 'singular' => '', 'other' => 'search-page' ],
]) );

update_option( 'mfn_header_entire_site', $tmpl_id );

printf( "header template=%d menu=%s sections=%d\n", $tmpl_id, $menu_id, count( $sections ) );
