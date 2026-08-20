<?php
/**
 * Shared BeBuilder generation helpers for the Miraex build scripts.
 *
 * Requires WordPress to be loaded already (the callers do that).
 */

/* ---------------------------------------------------------------- tokens */

$C = [
	'navy900'  => '#070f1c',
	'navy800'  => '#0a1b2e',
	'navy700'  => '#0b2545',
	'surface'  => '#0c1d33',
	'surface2' => '#0f2440',
	'ink'      => '#e8eef6',
	'inkSoft'  => '#aebdce',
	'slate'    => '#90a0b3',
	'cyan'     => '#19c6da',
	'cyanBr'   => '#34e2f0',
	'violet'   => '#7c6cf0',
	'line'     => 'rgba(255,255,255,0.10)',
	'lineSoft' => 'rgba(255,255,255,0.06)',
	'dark'     => '#04121b',
	'white'    => '#ffffff',
];

$F_DISPLAY = 'Space Grotesk';
$F_TEXT    = 'Inter';
$F_MONO    = 'JetBrains Mono';

$GRAD = 'linear-gradient(100deg,#19c6da 0%,#3b82f6 48%,#7c6cf0 100%)';

/* --------------------------------------------------------------- helpers */

const SEC   = '.mcb-section-mfnuidelement';
const SECW  = '.mcb-section-mfnuidelement .section_wrapper';
const WRP   = '.mcb-section .mcb-wrap-mfnuidelement';
const WRPI  = '.mcb-section .mcb-wrap-mfnuidelement > .mcb-wrap-inner';
const ITEM  = '.mcb-section .mcb-wrap .mcb-item-mfnuidelement';

function uid( $key ) {
	return substr( md5( 'miraex-home-v1|' . $key ), 0, 9 );
}

function css( $selector, $style, $val ) {
	return [ 'selector' => $selector, 'style' => $style, 'val' => $val ];
}

/** dimensions field WITH 'separated-fields' version (padding / margin) */
function dim( $top = '', $right = '', $bottom = '', $left = '' ) {
	/* Mfn_Helper::mfnLocalStyle() drops a side with empty( $val ) — and empty('0')
	   is true in PHP, so a bare '0' silently disappears from the generated CSS.
	   Normalise it here so no caller has to remember the 'px'. */
	$sides = [ 'top' => $top, 'right' => $right, 'bottom' => $bottom, 'left' => $left ];

	foreach ( $sides as $k => $v ) {
		if ( '0' === $v || 0 === $v ) {
			$sides[ $k ] = '0px';
		}
	}

	return $sides;
}

/** Side gutter on phones. Betheme's own .section_wrapper padding is 33px. */
const MOBILE_GUTTER = '15px';

function section( $key, $title, array $attr, array $wraps ) {
	$base = [
		/* Betheme pads .section_wrapper by 33px, which stacked on top of the section's
		   own padding left a ~53px gutter on phones. The wrapper carries the whole
		   gutter instead, so every section lines up at MOBILE_GUTTER. */
		'css_sec_gutter_mobile'      => css( SECW, 'padding', [
			'mobile' => dim( '0px', MOBILE_GUTTER, '0px', MOBILE_GUTTER ),
		] ),
		'scroll-visibility'          => 'show',
		'closeable-x'                => 'left',
		'background_switcher'        => 'default',
		'background_switcher_hover'  => 'default',
		'background_switcher_scroll' => 'default',
		'background_overlay_switcher'=> 'default',
	];

	return [
		'uid'     => uid( $key ),
		'attr'    => array_merge( $base, $attr ),
		'jsclass' => 'section',
		'title'   => $title,
		'icon'    => 'section',
		'wraps'   => $wraps,
	];
}

function wrap( $key, $title, $size, array $attr, array $items, $tablet = null, $mobile = '1/1' ) {
	$base = [
		'width_switcher'            => '',
		'background_switcher'       => 'default',
		'background_switcher_hover' => 'default',
	];

	/* BeBuilder shows the numeric width field only when Width = Custom, and the
	   `css_advanced_flex` value is conditional on it. Setting the value without the
	   switch leaves the UI reading "Default" while the CSS applies a fixed width —
	   and a save from the builder would then drop the width. Keep them in sync. */
	if ( isset( $attr['css_advanced_flex'] ) ) {
		$base['width_switcher'] = 'custom';
	}

	return [
		'uid'         => uid( $key ),
		'size'        => $size,
		'tablet_size' => $tablet ?: $size,
		'mobile_size' => $mobile,
		'attr'        => array_merge( $base, $attr ),
		'jsclass'     => 'wrap',
		'title'       => $title,
		'icon'        => 'wrap',
		'items'       => $items,
	];
}

function item( $key, $type, $title, $size, array $attr, $tablet = null, $mobile = '1/1' ) {
	return [
		'type'           => $type,
		'uid'            => uid( $key ),
		'size'           => $size,
		'tablet_size'    => $tablet ?: $size,
		'mobile_size'    => $mobile,
		'tablet_resized' => '0',
		'jsclass'        => $type,
		'title'          => $title,
		'icon'           => str_replace( '_', '-', $type ),
		'attr'           => $attr,
	];
}

/** attachment URL by its original source path on the reference site */
function media( $path ) {
	static $cache = null;

	if ( null === $cache ) {
		$cache = [];
		$base  = 'https://ashy-forest-0b7587303.7.azurestaticapps.net/';
		$posts = get_posts([
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'meta_key'    => '_miraex_src_url',
		]);
		foreach ( $posts as $p ) {
			$src = get_post_meta( $p->ID, '_miraex_src_url', true );
			$cache[ str_replace( $base, '', $src ) ] = [ 'id' => $p->ID, 'url' => wp_get_attachment_url( $p->ID ) ];
		}
	}

	if ( ! isset( $cache[ $path ] ) ) {
		fwrite( STDERR, "MISSING MEDIA: $path\n" );
		return [ 'id' => 0, 'url' => '' ];
	}

	return $cache[ $path ];
}

/* ------------------------------------------------------- style shortcuts */

/**
 * BeTheme gives every item column `margin-bottom: 40px`
 * (--mfn-column-gap-bottom). The reference design uses 16–18px between
 * elements, so every helper states its own vertical rhythm.
 */
function col_margin( $bottom, $top = '0px' ) {
	return [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( $top, '', $bottom, '' ) ] ) ];
}

/**
 * Turn a section's wraps into a horizontal, snap-scrolling rail on phones.
 *
 * Card grids stack to several screens tall on a phone; a swipeable rail keeps the
 * section one screen high. CSS only — no slider script, and the desktop grid is
 * untouched. Apply to a section whose wraps are *only* the cards.
 */
function mobile_rail( $card_width = '82%' ) {
	return [
		'css_rail_nowrap'    => css( SECW, 'flex-wrap',           [ 'mobile' => 'nowrap' ] ),
		'css_rail_overflow'  => css( SECW, 'overflow-x',          [ 'mobile' => 'auto' ] ),
		'css_rail_snap'      => css( SECW, 'scroll-snap-type',    [ 'mobile' => 'x mandatory' ] ),
		'css_rail_scrollpad' => css( SECW, 'scroll-padding-left', [ 'mobile' => MOBILE_GUTTER ] ),
		'css_rail_gap'       => css( SECW, 'column-gap',          [ 'mobile' => '12px' ] ),
		'css_rail_sbar'      => css( SECW, 'scrollbar-width',     [ 'mobile' => 'none' ] ),
		/* mfnLocalStyle turns `flex` into "flex:0 0 <val>" — a rail item exactly */
		'css_rail_item'      => css( SECW . ' > .wrap', 'flex',              [ 'mobile' => $card_width ] ),
		'css_rail_item_snap' => css( SECW . ' > .wrap', 'scroll-snap-align', [ 'mobile' => 'start' ] ),
	];
}

/** ".btn-row{display:flex;gap:14px;flex-wrap:wrap}" — apply to any wrap holding buttons. */
function btn_row( $justify = 'flex-start' ) {
	return [
		'css_advanced_gap'             => css( WRPI, 'column-gap', '14px' ),
		/* .btn-row{gap:14px} is both axes — the buttons zero their column margins, so
		   without this they touch as soon as the row wraps (phones). */
		'css_advanced_row_gap'         => css( WRPI, 'row-gap', '14px' ),
		'css_advanced_flex_wrap'       => css( WRPI, 'flex-wrap', [ 'desktop' => 'wrap' ] ),
		'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => $justify ] ),
	];
}

/** Hover treatment shared by every card frame (.card / .imgcard in app.css). */
function card_hover( $selector ) {
	return [
		'css_advanced_transition'       => css( $selector, 'transition', '0.28s cubic-bezier(0.2,0.7,0.2,1)' ),
		'css_advanced_border_hover'     => css( $selector . ':hover', 'border-color', 'rgba(25,198,218,0.40)' ),
		'css_advanced_transform_hover'  => css( $selector . ':hover', 'transform', 'translateY(-4px)' ),
		'css_advanced_box_shadow_hover' => css( $selector . ':hover', 'box-shadow', '0 18px 50px -20px rgba(0,0,0,0.7)' ),
	];
}

/** eyebrow: mono, cyan, tracked-out label */
function eyebrow( $key, $text, $align = 'left' ) {
	global $C, $F_MONO;

	return item( $key, 'plain_text', 'Eyebrow', '1/1', array_merge( col_margin( '18px' ), [
		'content'                => $text,
		'css_descdesca_color'    => css( ITEM . ' .desc', 'color', $C['cyan'] ),
		'css_desc_text_align'    => css( ITEM . ' .desc', 'text-align', $align ),
		'css_desc_typography'    => css( ITEM . ' .desc', 'typography', [
			'desktop'        => [ 'font-size' => '12.5px', 'line-height' => '1.4', 'font-family' => $F_MONO ],
			'font-weight'    => '500',
			'letter-spacing' => '0.28em',
			'text-transform' => 'uppercase',
		] ),
	] ) );
}

function heading_item( $key, $text, $tag, $size_px, $mobile_px, $align = 'left', $link = '', $extra = [] ) {
	global $C, $F_DISPLAY;

	$attr = [
		'title'          => $text,
		'header_tag'     => $tag,
		'css_txt_align'  => css( ITEM . ' .title', 'text-align', $align ),
		'css_color'      => css( ITEM . ' .title,' . ITEM . ' .title a', 'color', $C['white'] ),
		'css_typography' => css( ITEM . ' .title', 'typography', [
			'desktop'        => [ 'font-size' => $size_px, 'line-height' => '1.08', 'font-family' => $F_DISPLAY ],
			'mobile'         => [ 'font-size' => $mobile_px, 'line-height' => '1.12' ],
			'font-weight'    => '600',
			'letter-spacing' => '-0.02em',
		] ),
	];

	if ( $link ) {
		$attr['link']       = $link;
		$attr['link_type']  = '';
		$attr['css_color_hover'] = css( ITEM . ' .title:hover,' . ITEM . ' .title a:hover', 'color', $C['cyan'] );
	}

	return item( $key, 'heading', 'Heading', '1/1', array_merge( col_margin( '18px' ), $attr, $extra ) );
}

function text_item( $key, $html, $opts = [] ) {
	global $C, $F_TEXT;

	$color = $opts['color'] ?? $C['inkSoft'];
	$size  = $opts['size']  ?? '17px';
	$lh    = $opts['lh']    ?? '1.65';
	$align = $opts['align'] ?? 'left';
	$title = $opts['title'] ?? 'Text';
	$isize = $opts['item_size'] ?? '1/1';

	return item( $key, 'plain_text', $title, $isize, array_merge( col_margin( '16px' ), [
		'content'              => $html,
		'css_descdesca_color'  => css( ITEM . ' .desc', 'color', $color ),
		'css_desc_text_align'  => css( ITEM . ' .desc', 'text-align', $align ),
		'css_desc_links_color' => css( ITEM . ' .desc a', 'color', $C['cyan'] ),
		'css_desc_links_color_hover' => css( ITEM . ' .desc a:hover', 'color', $C['cyanBr'] ),
		'css_desc_typography'  => css( ITEM . ' .desc', 'typography', [
			'desktop'     => [ 'font-size' => $size, 'line-height' => $lh, 'font-family' => $F_TEXT ],
			'font-weight' => $opts['weight'] ?? '400',
		] ),
	], $opts['extra'] ?? [] ) );
}

function button_item( $key, $label, $link, $style = 'primary', $size = '1/4', $align = '', $lg = false, array $extra = [] ) {
	global $C, $F_TEXT;

	$is_primary = 'primary' === $style;

	$attr = [
		'title'                    => $label,
		'link'                     => $link,
		/* Betheme implements button "size" as transform:scale() — .button_size_1/3/4 are
		   0.9/1.1/1.2. Any of those scales the rendered button away from the padding and
		   font-size set below (and makes the 14px row gap look like 0). '2' is the
		   unscaled size; the reference values are applied as plain CSS instead. */
		'size'                     => '2',
		'align'                    => $align,
		/* .btn-row{display:flex;gap:14px} — a button is content-sized, not a column.
		   Sizing it as 1/4 or 1/2 is what made the gaps swing between 0 and 95px. */
		'width_switcher'           => 'inline',
		'button_style'             => '',
		/* html-redesign: every .btn-primary carries the arrow, .btn-ghost does not —
		   except the standalone inline ghost links, which pass `icon` through $extra. */
		'icon'                     => $is_primary ? 'fas fa-arrow-right' : '',
		'icon_position'            => 'right',
		'css_button_typography'    => css( ITEM . ' .button', 'typography', [
			/* .btn has no line-height of its own — it inherits body{line-height:1.65}.
			   Betheme's button line-height is 1.2, which is 7px shorter. */
			'desktop'        => [ 'font-size' => $lg ? '16px' : '15px', 'line-height' => '1.65', 'font-family' => $F_TEXT ],
			'font-weight'    => '600',
			'letter-spacing' => '0.01em',
		] ),
		/* .btn svg{width:18px;height:18px;flex:none} — the Font Awesome glyph is only
		   ~14px wide, so the icon box is sized explicitly to keep the button width. */
		'css_button_icon_box'      => css( ITEM . ' .button .button_icon', 'width', '18px' ),
		'css_button_icon_size'     => css( ITEM . ' .button .button_icon i', 'typography', [
			'desktop' => [ 'font-size' => '17px', 'line-height' => '1' ],
		] ),
		'css_button_padding'       => css( ITEM . ' .button', 'padding', [
			'desktop' => $lg ? dim( '17px', '32px', '17px', '32px' ) : dim( '14px', '26px', '14px', '26px' ),
		] ),
		'css_button_border_radius' => css( ITEM . ' .button', 'border-radius', [ 'desktop' => '999px 999px 999px 999px' ] ),
		'css_button_border_style'  => css( ITEM . ' .button', 'border-style', 'solid' ),
		'css_button_border_width'  => css( ITEM . ' .button', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
	];

	if ( $is_primary ) {
		$attr['css_button_color']            = css( ITEM . ' .button', 'color', $C['dark'] );
		$attr['css_button_icon_color']       = css( ITEM . ' .button i', 'color', $C['dark'] );
		$attr['css_button_background_color'] = css( ITEM . ' .button', 'background-color', $C['cyan'] );
		$attr['css_button_border_color']     = css( ITEM . ' .button', 'border-color', $C['cyan'] );
		$attr['css_button_background_hover'] = css( ITEM . ' .button:hover, ' . ITEM . ' .button:before', 'background', $C['cyanBr'] );
		$attr['css_button_border_color_hover'] = css( ITEM . ' .button:hover', 'border-color', $C['cyanBr'] );
		$attr['css_button_color_hover']      = css( ITEM . ' .button:hover', 'color', $C['dark'] );
	} else {
		$attr['css_button_color']            = css( ITEM . ' .button', 'color', $C['white'] );
		$attr['css_button_icon_color']       = css( ITEM . ' .button i', 'color', $C['white'] );
		$attr['css_button_background_color'] = css( ITEM . ' .button', 'background-color', 'rgba(255,255,255,0.02)' );
		$attr['css_button_border_color']     = css( ITEM . ' .button', 'border-color', $C['line'] );
		$attr['css_button_background_hover'] = css( ITEM . ' .button:hover, ' . ITEM . ' .button:before', 'background', 'rgba(25,198,218,0.08)' );
		$attr['css_button_border_color_hover'] = css( ITEM . ' .button:hover', 'border-color', $C['cyan'] );
		$attr['css_button_color_hover']      = css( ITEM . ' .button:hover', 'color', $C['white'] );
	}

	/* No column margins either: the 14px comes from the row's column-gap, and
	   `.mfn-item-inline` shrink-wraps the column to the button. */
	$flush = [
		'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
		'css_btn_col_align'   => css( ITEM . ' .mcb-column-inner', 'text-align', 'left' ),
	];

	return item( $key, 'button', $label, $size, array_merge( $flush, $attr, $extra ), '1/2', '1/1' );
}

/** card-like wrap: surface bg, hairline border, rounded, padded */
function card_attr( $pad = '30px', $bg = null, $border = null, $radius = '14px', $hover = true ) {
	global $C;

	$bg     = $bg ?: $C['surface'];
	$border = $border ?: $C['line'];

	$attr = [
		/* .grid{gap:24px} — BeTheme wraps sit flush, so the gutter goes on the
		   outer wrap and the card frame stays on the inner element. */
		'css_advanced_gutter'           => css( WRP, 'padding', [ 'desktop' => dim( '0px', '12px', '24px', '12px' ), 'mobile' => dim( '0px', '0px', '20px', '0px' ) ] ),
		'css_advanced_align_self'       => css( WRP, 'align-self', [ 'desktop' => 'stretch' ] ),
		'css_advanced_height'           => css( WRPI, 'height', [ 'desktop' => '100%' ] ),
		'css_advanced_background_color' => css( WRPI, 'background-color', $bg ),
		'css_advanced_border_style'     => css( WRPI, 'border-style', 'solid' ),
		'css_advanced_border_color'     => css( WRPI, 'border-color', $border ),
		'css_advanced_border_width'     => css( WRPI, 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_advanced_border_radius'    => css( WRPI, 'border-radius', [ 'desktop' => "$radius $radius $radius $radius" ] ),
		'css_advanced_padding'          => css( WRPI, 'padding', [ 'desktop' => dim( $pad, $pad, $pad, $pad ) ] ),
	];

	return $hover ? array_merge( $attr, card_hover( WRPI ) ) : $attr;
}

function section_pad( $top, $bottom, $mtop = null, $mbottom = null ) {
	$val = [ 'desktop' => dim( $top, '', $bottom, '' ) ];

	if ( $mtop ) {
		/* no horizontal padding on phones — the gutter lives on .section_wrapper */
		$val['mobile'] = dim( $mtop, '0px', $mbottom ?: $mtop, '0px' );
	}

	return css( SEC, 'padding', $val );
}

/* ------------------------------------------------------- persistence ---- */

/** Flatten sections → wraps → items into the list preparePostUpdate() expects. */
function mfn_flatten( array $nodes, array &$flat = null ) {
	if ( null === $flat ) {
		$flat = [];
	}

	foreach ( $nodes as $n ) {
		$copy = $n;
		unset( $copy['wraps'], $copy['items'] );
		$flat[] = $copy;

		foreach ( [ 'wraps', 'items' ] as $k ) {
			if ( ! empty( $n[ $k ] ) ) {
				mfn_flatten( $n[ $k ], $flat );
			}
		}
	}

	return $flat;
}

/** Plain-text dump of the builder content, used by BeTheme for SEO plugins. */
function mfn_seo_text( array $nodes, $out = '' ) {
	foreach ( $nodes as $n ) {
		foreach ( [ 'title', 'heading', 'content' ] as $k ) {
			if ( ! empty( $n['attr'][ $k ] ) && is_string( $n['attr'][ $k ] ) ) {
				$out .= "\n" . $n['attr'][ $k ];
			}
		}
		foreach ( [ 'wraps', 'items' ] as $k ) {
			if ( ! empty( $n[ $k ] ) ) {
				$out = mfn_seo_text( $n[ $k ], $out );
			}
		}
	}

	return $out;
}

/**
 * Write builder content to a post/template and regenerate its local style CSS
 * exactly the way BeBuilder's own save routine does.
 */
function mfn_store_template( $post_id, array $sections ) {
	update_post_meta( $post_id, 'mfn-page-items', wp_slash( $sections ) );
	update_post_meta( $post_id, 'mfn-page-items-seo', mfn_seo_text( $sections ) );

	delete_post_meta( $post_id, 'mfn-page-object' );
	delete_post_meta( $post_id, 'mfn-builder-preview' );
	delete_post_meta( $post_id, 'mfn-builder-preview-local-style' );
	delete_post_meta( $post_id, 'mfn-builder-revision-autosave' );
	delete_post_meta( $post_id, 'mfn-builder-revision-update' );

	require_once get_theme_file_path( '/functions/admin/class-mfn-helper.php' );

	$flat = mfn_flatten( $sections );
	Mfn_Helper::preparePostUpdate( $flat, $post_id );

	return count( $flat );
}
