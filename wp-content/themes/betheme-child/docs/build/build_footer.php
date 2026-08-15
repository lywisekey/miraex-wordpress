<?php
/**
 * Build the "Footer Miraex" BeBuilder footer template from
 * html-redesign/index.html (.site-footer → .foot-grid + .foot-bottom).
 *
 * Idempotent.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';

$logo = media( 'assets/img/miraex-logo.png' );

/* ---------------------------------------------------------------- pieces */

/** Column heading: mono, uppercase, tracked out — .foot-grid h5 */
function ftr_heading( $key, $label ) {
	global $C, $F_MONO;

	return item( $key, 'heading', $label, '1/1', array_merge( col_margin( '18px' ), [
		'title'          => $label,
		'header_tag'     => 'h5',
		'css_color'      => css( ITEM . ' .title,' . ITEM . ' .title a', 'color', $C['slate'] ),
		'css_typography' => css( ITEM . ' .title', 'typography', [
			'desktop'        => [ 'font-size' => '12px', 'line-height' => '1.4', 'font-family' => $F_MONO ],
			'font-weight'    => '500',
			'letter-spacing' => '0.12em',
			'text-transform' => 'uppercase',
		] ),
	] ) );
}

/** Stacked link list — .foot-grid a */
function ftr_links( $key, array $links ) {
	global $C, $F_TEXT;

	$html = '';
	foreach ( $links as $label => $url ) {
		$blank = ( 0 === strpos( $url, 'http' ) ) ? ' target="_blank" rel="noopener"' : '';
		$html .= '<a href="' . $url . '"' . $blank . ' style="display:block;padding:6px 0">' . $label . '</a>';
	}

	return item( $key, 'plain_text', 'Links', '1/1', array_merge( col_margin( '0px' ), [
		'content'              => $html,
		'css_descdesca_color'  => css( ITEM . ' .desc', 'color', $C['inkSoft'] ),
		'css_desc_links_color' => css( ITEM . ' .desc a', 'color', $C['inkSoft'] ),
		'css_desc_links_color_hover' => css( ITEM . ' .desc a:hover', 'color', $C['cyan'] ),
		'css_desc_typography'  => css( ITEM . ' .desc', 'typography', [
			'desktop'     => [ 'font-size' => '14.5px', 'line-height' => '1.45', 'font-family' => $F_TEXT ],
			'font-weight' => '400',
		] ),
	] ) );
}

/* -------------------------------------------------------------- sections */

$sections = [];

/* ---- 1. link grid ----
 *
 * The four column widths were set by hand in BeBuilder and are reproduced here so that
 * re-running this script cannot undo them. They have to add up:
 *
 *     440 + 250 + 180 + 230 = 1100   +   3 gaps x 40px = 120   =   1220px
 *
 * which is exactly the container. The original 400/250/250/250 came to 1270 — 50px over,
 * which is what pushed the last column onto its own line. Change one width and another
 * has to give the space back.
 */

$sections[] = section( 'ftr-grid', 'Footer — links', [
	'css_advanced_padding'          => css( SEC, 'padding', [
		'desktop' => dim( '96px', '', '48px', '' ),
		'mobile'  => dim( '64px', '0px !important', '32px', '0px !important' ),
	] ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
	/* BeTheme rewrites a literal `.mcb-column-inner` in a custom selector into the
	   section-uid'd class, which matches nothing — so the side gutter is removed by
	   redefining the variable the margin is built from (set on body / .mfn-header-tmpl). */
	'css_ftr_col_gutter_l'           => css( SEC, '--mfn-column-gap-left',  [ 'mobile' => '0px' ] ),
	'css_ftr_col_gutter_r'           => css( SEC, '--mfn-column-gap-right', [ 'mobile' => '0px' ] ),
	'css_advanced_border_style'     => css( SEC, 'border-style', 'solid' ),
	'css_advanced_border_color'     => css( SEC, 'border-color', $C['line'] ),
	'css_advanced_border_width'     => css( SEC, 'border-width', [ 'desktop' => '1px 0 0 0' ] ),
	/* .foot-grid{grid-template-columns:1.6fr 1fr 1fr 1fr;gap:40px} */
	'css_advanced_gap'              => css( SECW, 'column-gap', '40px' ),
], [
	wrap( 'ftr-about', 'About', '2/5', [
		'css_advanced_flex' => css( WRP, 'width', [ 'desktop' => '440px', 'tablet' => '100%', 'mobile' => '100%' ] ),
	], [
		item( 'ftr-logo', 'image', 'Miraex logo', '1/1', [
			'src'       => $logo['url'],
			'size'      => 'full',
			'alt'       => 'Miraex — Connecting Quantum',
			'link'      => '/',
			'link_type' => 'custom',
			'width'     => '210',
			'css_image_logo_height' => css( ITEM . ' .image_frame img', 'height', '46px' ),
			'css_image_logo_width'  => css( ITEM . ' .image_frame img', 'width', 'auto' ),
			'css_image_frame_width' => css( ITEM . ' .image_frame', 'width', 'auto' ),
			'width_switcher'        => 'inline',
			/* keep BeTheme's 12px column gutter on the left so the logo lines up with the
			   page content; the 14px on the right is the gap to the co-brand lockup */
			'css_advanced_margin'   => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '14px', '0px', '12px' ) ] ),
		] ),
		text_item( 'ftr-cobrand', '<span style="display:inline-block;font-family:\'JetBrains Mono\',monospace;font-size:9.5px;line-height:1.25;letter-spacing:0.16em;text-transform:uppercase;color:#aebdce;padding-left:14px;border-left:1px solid rgba(255,255,255,0.10)">a SEALSQ<br>company</span>', [
			'title' => 'Co-brand',
			'extra' => [
				'width_switcher'      => 'inline',
				'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
			],
		] ),
		text_item( 'ftr-about-text', 'Miraex builds thin-film lithium tantalate photonic integrated circuits — the quantum interconnect layer that links quantum processors, sensors and networks into one coherent infrastructure.', [
			'size' => '14.5px', 'lh' => '1.6', 'title' => 'About',
			/* .foot-about p{max-width:300px;margin:16px 0 20px} */
			'extra' => array_merge( col_margin( '20px', '16px' ), [
				'css_about_maxwidth' => css( ITEM . ' .mcb-column-inner', 'max-width', '300px' ),
			] ),
		] ),
		text_item( 'ftr-seal', '<span style="display:inline-flex;align-items:center;gap:10px;font-size:13px;color:#aebdce"><span style="font-family:\'JetBrains Mono\',monospace;font-size:11px;color:#04121b;background:#19c6da;padding:4px 10px;border-radius:999px">SEALSQ</span> Part of the SEALSQ Quantum Sovereign Vertical Stack</span>', [
			'title' => 'SEALSQ badge',
			'extra' => col_margin( '0px', '18px' ),   /* .foot-seal{margin-top:18px} */
		] ),
	], '1/1', '1/1' ),

	wrap( 'ftr-col-solutions', 'Solutions', '1/5', [
		'css_advanced_flex' => css( WRP, 'width', [ 'desktop' => '250px', 'tablet' => '100%', 'mobile' => '100%' ] ),
	], [
		ftr_heading( 'ftr-h-solutions', 'Solutions' ),
		ftr_links( 'ftr-l-solutions', [
			'Distributed Quantum Computing' => '/distributed-quantum-computing/',
			'Quantum Sensing'               => '/quantum-sensing/',
			'Quantum Networking'            => '/quantum-networking/',
			'TFLT PIC Platform'             => '/technology/',
		] ),
	], '1/3', '1/1' ),

	wrap( 'ftr-col-company', 'Company', '1/5', [
		'css_advanced_flex' => css( WRP, 'width', [ 'desktop' => '180px', 'tablet' => '100%', 'mobile' => '100%' ] ),
	], [
		ftr_heading( 'ftr-h-company', 'Company' ),
		ftr_links( 'ftr-l-company', [
			'About'     => '/about/',
			'News'      => '/news/',
			'Careers'   => '/careers/',
			'Resources' => '/resources/',
			'Contact'   => '/contact/',
		] ),
	], '1/3', '1/1' ),

	wrap( 'ftr-col-group', 'Group', '1/5', [
		'css_advanced_flex' => css( WRP, 'width', [ 'desktop' => '230px', 'tablet' => '100%', 'mobile' => '100%' ] ),
	], [
		ftr_heading( 'ftr-h-group', 'Group' ),
		ftr_links( 'ftr-l-group', [
			'SEALSQ (NASDAQ: LAES)' => 'https://www.sealsq.com/',
			'WISeKey (NASDAQ: WKEY)' => 'https://www.wisekey.com/',
		] ),
	], '1/3', '1/1' ),
] );

/* ---- 2. bottom bar ---- */

$sections[] = section( 'ftr-bottom', 'Footer — bottom bar', [
	'css_advanced_padding'          => css( SEC, 'padding', [
		'desktop' => dim( '26px', '', '32px', '' ),
		'mobile'  => dim( '24px', '0px !important', '28px', '0px !important' ),
	] ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
	/* BeTheme rewrites a literal `.mcb-column-inner` in a custom selector into the
	   section-uid'd class, which matches nothing — so the side gutter is removed by
	   redefining the variable the margin is built from (set on body / .mfn-header-tmpl). */
	'css_ftr_col_gutter_l'           => css( SEC, '--mfn-column-gap-left',  [ 'mobile' => '0px' ] ),
	'css_ftr_col_gutter_r'           => css( SEC, '--mfn-column-gap-right', [ 'mobile' => '0px' ] ),
	'css_advanced_border_style'     => css( SEC, 'border-style', 'solid' ),
	'css_advanced_border_color'     => css( SEC, 'border-color', $C['lineSoft'] ),
	'css_advanced_border_width'     => css( SEC, 'border-width', [ 'desktop' => '1px 0 0 0' ] ),
	'css_advanced_align_items'      => css( SECW, 'align-items', [ 'desktop' => 'center' ] ),
], [
	wrap( 'ftr-copy', 'Copyright', '1/2', [
		'css_advanced_align_items' => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
	], [
		text_item( 'ftr-copy-text', '© ' . gmdate( 'Y' ) . ' Miraex SA — a SEALSQ company. All rights reserved.', [
			'color' => $C['slate'], 'size' => '13px', 'title' => 'Copyright',
			'extra' => col_margin( '0px' ),
		] ),
	], '1/2', '1/1' ),

	wrap( 'ftr-badges', 'Badges', '1/2', [
		'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
		'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-end' ] ),
	], [
		text_item( 'ftr-badges-text',
			'<span style="display:inline-flex;gap:10px;flex-wrap:wrap;font-family:\'JetBrains Mono\',monospace;font-size:11px">'
			. '<span style="color:#aebdce;border:1px solid rgba(255,255,255,0.10);padding:6px 12px;border-radius:999px">Ecublens · Switzerland</span>'
			. '<a href="/privacy/" style="color:#aebdce;border:1px solid rgba(255,255,255,0.10);padding:6px 12px;border-radius:999px">Privacy</a>'
			. '<a href="/terms-of-service/" style="color:#aebdce;border:1px solid rgba(255,255,255,0.10);padding:6px 12px;border-radius:999px">Terms of Service</a>'
			. '</span>',
			[ 'align' => 'right', 'title' => 'Badges', 'extra' => col_margin( '0px' ) ]
		),
	], '1/2', '1/1' ),
] );

/* ---------------------------------------------------------- persistence  */

$existing = get_posts([
	'post_type'   => 'template',
	'numberposts' => 1,
	'meta_key'    => 'mfn_template_type',
	'meta_value'  => 'footer',
	'post_status' => 'any',
]);

if ( $existing ) {
	$tmpl_id = $existing[0]->ID;
	wp_update_post([ 'ID' => $tmpl_id, 'post_title' => 'Footer Miraex', 'post_status' => 'publish' ]);
} else {
	$tmpl_id = wp_insert_post([
		'post_type'   => 'template',
		'post_title'  => 'Footer Miraex',
		'post_name'   => 'footer-miraex',
		'post_status' => 'publish',
		'post_author' => 1,
	]);
	update_post_meta( $tmpl_id, 'mfn_template_type', 'footer' );
}

$nodes = mfn_store_template( $tmpl_id, $sections );

update_post_meta( $tmpl_id, 'mfn_template_conditions', wp_json_encode([
	[ 'rule' => 'include', 'var' => 'everywhere', 'archives' => '', 'singular' => '', 'other' => 'search-page' ],
]) );

update_option( 'mfn_footer_entire_site', $tmpl_id );

printf( "footer template=%d sections=%d nodes=%d\n", $tmpl_id, count( $sections ), $nodes );
