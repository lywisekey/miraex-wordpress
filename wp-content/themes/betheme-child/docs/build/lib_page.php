<?php
/**
 * Page-level BeBuilder components shared by the Miraex inner pages.
 *
 * These mirror the components in html-redesign/css/app.css:
 *   .page-hero / .crumbs / .glowfx · .split · .ticks · table.spec · .app
 *   .card.feature · .cta-band · .timeline · .acc
 *
 * Requires lib.php.
 */

/* ------------------------------------------------------------ primitives */

/** Section head: eyebrow + h2 (+ optional lead). Returns an array of items. */
/* .h2{font-size:clamp(2rem,3.6vw,3rem)} -> 48px at 1440px */
function head_items( $key, $eyebrow, $h2, $lead = '', $align = 'left', $h2_size = '48px' ) {
	$items = [];

	if ( $eyebrow ) {
		$items[] = eyebrow( $key . '-eyebrow', $eyebrow, $align );
	}

	$items[] = heading_item( $key . '-h2', $h2, 'h2', $h2_size, '32px', $align );

	if ( $lead ) {
		$items[] = text_item( $key . '-lead', $lead, [
			'size' => '20.8px', 'lh' => '1.55', 'align' => $align, 'title' => 'Lead',
		] );
	}

	return $items;
}

/** ".ticks" — checklist rows. */
function tick_items( $key, array $rows ) {
	global $C, $F_TEXT;

	$items = [];

	foreach ( $rows as $i => $html ) {
		$items[] = item( $key . '-tick' . $i, 'list', 'Tick', '1/1', array_merge( col_margin( '2px' ), [
			'icon'      => 'fas fa-check',
			'title'     => '',
			'title_tag' => 'h4',
			'content'   => $html,
			'style'     => 1,
			'css_list_rightdesc_color'      => css( ITEM . ' .list_right .desc', 'color', $C['inkSoft'] ),
			'css_list_rightdesc_typography' => css( ITEM . ' .list_right .desc', 'typography', [
				'desktop' => [ 'font-size' => '15.5px', 'line-height' => '1.6', 'font-family' => $F_TEXT ],
			] ),
			'css_list_lefticircle_color'      => css( ITEM . ' .list_left i,' . ITEM . ' .circle', 'color', $C['cyan'] ),
			'css_list_lefticircle_font_size'  => css( ITEM . ' .list_left i,' . ITEM . ' .circle', 'font-size', [ 'desktop' => '20px' ] ),
			'css_list_leftcircle_background'  => css( ITEM . ' .list_left,' . ITEM . ' .circle', 'background', 'transparent' ),
			'css_list_leftcircle_border_style'=> css( ITEM . ' .list_left,' . ITEM . ' .circle', 'border-style', 'none' ),
			/* .ticks li svg{width:20px;height:20px;margin-top:3px} — replaces the 80x80 tile */
			'css_tick_left_width'   => css( ITEM . ' .list_item .list_left', 'width', '20px' ),
			'css_tick_left_height'  => css( ITEM . ' .list_item .list_left', 'height', '20px' ),
			'css_tick_left_margin'  => css( ITEM . ' .list_item .list_left', 'margin', '3px 13px 0px 0px' ),
			'css_tick_left_radius'  => css( ITEM . ' .list_item .list_left', 'border-radius', '0px' ),
			'css_tick_left_shadow'  => css( ITEM . ' .list_item .list_left', 'box-shadow', 'none' ),
			'css_tick_left_bgimg'   => css( ITEM . ' .list_item .list_left', 'background-image', 'none' ),
			/* .list_right{padding:5px 0 0;margin-left:100px} -> 20px icon + 13px gap */
			'css_tick_right_margin' => css( ITEM . ' .list_item .list_right', 'margin-left', '33px' ),
			'css_tick_right_pad'    => css( ITEM . ' .list_item .list_right', 'padding-top', '0px' ),
			/* .ticks li b{color:#fff;font-weight:600} */
			'css_tick_bold_color'   => css( ITEM . ' .list_right .desc strong', 'color', $C['white'] ),
			'css_tick_bold_weight'  => css( ITEM . ' .list_right .desc strong', 'font-weight', '600' ),
			/* .ticks li{padding:9px 0} */
			'css_tick_row_pad'      => css( ITEM . ' .list_item', 'padding', '9px 0px' ),
		] ) );
	}

	return $items;
}

/** "table.spec" — two-column specification table. */
function spec_table( $key, array $rows ) {
	$html = '<table style="width:100%;border-collapse:collapse;border:1px solid rgba(255,255,255,0.10);border-radius:14px;overflow:hidden">';

	foreach ( $rows as $label => $value ) {
		$html .= '<tr>'
			. '<td style="width:45%;padding:15px 20px;border-bottom:1px solid rgba(255,255,255,0.06);color:#aebdce;font-size:15px;vertical-align:top;text-align:left">' . $label . '</td>'
			. '<td style="padding:15px 20px;border-bottom:1px solid rgba(255,255,255,0.06);color:#ffffff;font-family:\'JetBrains Mono\',monospace;font-size:14px;line-height:1.5;text-align:left">' . $value . '</td>'
			. '</tr>';
	}

	$html .= '</table>';

	return text_item( $key, $html, [
		'title' => 'Spec table',
		'size'  => '14.5px',
		'extra' => [
			'css_spec_row_transition' => css( ITEM . ' table td', 'transition', '0.2s' ),
			'css_spec_row_hover'      => css( ITEM . ' table tr:hover td', 'background-color', 'rgba(25,198,218,0.04)' ),
		],
	] );
}

/** ".app" cards as section-level wraps — for grids that sit directly in a section. */
function app_card_wraps( $key, array $rows, $size = '1/1' ) {
	global $C;

	$wraps = [];

	foreach ( $rows as $i => $row ) {
		$attr = card_attr( '22px', 'rgba(255,255,255,0.015)' );
		$attr['css_advanced_margin']      = css( WRPI, 'margin', [ 'desktop' => dim( '0px', '', '14px', '' ) ] );
		$attr['css_advanced_align_items'] = css( WRPI, 'align-items', [ 'desktop' => 'flex-start' ] );

		$flush = [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', '0px', '' ) ] ) ];

		$wraps[] = wrap( $key . '-w' . $i, $row[1], $size, $attr, [
			text_item( $key . '-n' . $i, $row[0], [
				'color' => $C['cyan'], 'size' => '13px', 'weight' => '500',
				'title' => 'Label', 'item_size' => '1/6', 'extra' => $flush,
			] ),
			text_item( $key . '-b' . $i,
				'<b style="display:block;color:#fff;font-size:15.5px;margin-bottom:4px">' . $row[1] . '</b>'
				. '<span style="display:block;color:#aebdce;font-size:14.5px;line-height:1.55">' . $row[2] . '</span>',
				[ 'title' => $row[1], 'item_size' => '5/6', 'extra' => $flush ]
			),
		], $size, '1/1' );
	}

	return $wraps;
}

/** ".card.feature" — icon + title + copy (+ optional link or meta label). */
function feature_card( $key, $icon, $title, $desc, $link = '', $more = '', $more_muted = false ) {
	global $C, $F_DISPLAY, $F_TEXT;

	$items = [
		item( $key . '-box', 'icon_box', $title, '1/1', array_merge( col_margin( '10px' ), [
			'title'         => $title,
			'title_tag'     => 'h3',
			'content'       => $desc,
			'icon'          => $icon,
			'icon_position' => 'top',
			'link'          => $link,
			'border'        => 0,
			'css_icon_boxicon_wrappericon_color'     => css( ITEM . ' .icon_box .icon_wrapper .icon', 'color', $C['cyan'] ),
			'css_icon_boxicon_wrapperi_font_size'    => css( ITEM . ' .icon_box .icon_wrapper i', 'font-size', [ 'desktop' => '24px' ] ),
			'css_icon_boxicon_wrapper_background'    => css( ITEM . ' .icon_box .icon_wrapper', 'background', 'rgba(25,198,218,0.10)' ),
			'css_icon_boxicon_wrapper_border_style'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-style', 'solid' ),
			'css_icon_boxicon_wrapper_border_color'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-color', $C['line'] ),
			'css_icon_boxicon_wrapper_border_width'  => css( ITEM . ' .icon_box .icon_wrapper', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
			'css_icon_boxicon_wrapper_border_radius' => css( ITEM . ' .icon_box .icon_wrapper', 'border-radius', [ 'desktop' => '13px 13px 13px 13px' ] ),
			'css_icon_boxicon_wrapper_width'         => css( ITEM . ' .icon_box .icon_wrapper', 'width', '52px' ),
			'css_icon_boxicon_wrapper_height'        => css( ITEM . ' .icon_box .icon_wrapper', 'height', '52px' ),
			'css_icon_boxicon_wrapper_lineheight'    => css( ITEM . ' .icon_box .icon_wrapper', 'line-height', '1' ),
			'css_icon_boxicon_wrapper_fontsize'    => css( ITEM . ' .icon_box .icon_wrapper', 'font-size', '24px' ),
			'css_icon_boxicon_wrapper_margin'        => css( ITEM . ' .icon_box .icon_wrapper', 'margin', [ 'desktop' => dim( '0px', '0px', '20px', '0px' ) ] ),
			/* BeTheme defaults the reference does not have:
			     .icon_box:hover .icon_wrapper:before{background-color:#0089F7}  (theme accent)
			     .icon_box:hover .icon_wrapper{transform:scale(.94)}
			     .icon_box:hover .icon_wrapper i{top:-5px}
			     .icon_box:hover .desc_wrapper .title:before{width:100px} */
			'css_icon_boxhovericon_wrappericon_boxhovericon_wrapperbefore_background_hover' => css( ITEM . ' .icon_box:hover .icon_wrapper:before,' . ITEM . ' .icon_box a:hover .icon_wrapper:before', 'background-color', 'transparent' ),
			'css_icon_hover_no_scale'  => css( ITEM . ' .icon_box:hover .icon_wrapper', 'transform', 'none' ),
			'css_icon_hover_no_jump'   => css( ITEM . ' .icon_box:hover .icon_wrapper i', 'top', '0px' ),
			'css_icon_hover_no_rule'   => css( ITEM . ' .icon_box:hover .desc_wrapper .title:before', 'width', '0px' ),
			'css_icon_before_radius'   => css( ITEM . ' .icon_box .icon_wrapper:before', 'border-radius', '13px' ),
			'css_icon_boxsizing'       => css( ITEM . ' .icon_box .icon_wrapper', 'box-sizing', 'border-box' ),
			'css_icon_before_border'   => css( ITEM . ' .icon_box .icon_wrapper:before', 'border-color', 'transparent' ),
			/* .card .ico{margin-bottom:20px} and .card text are left aligned in app.css */
			'css_icon_box_desc_align'  => css( ITEM . ' .icon_box .desc_wrapper', 'text-align', 'left' ),
			'css_icon_boxdesc_wrappertitle_color'    => css( ITEM . ' .icon_box .desc_wrapper .title', 'color', $C['white'] ),
			'css_icon_boxdesc_wrappertitle_typography' => css( ITEM . ' .icon_box .desc_wrapper .title', 'typography', [
				'desktop'        => [ 'font-size' => '21px', 'line-height' => '1.2', 'font-family' => $F_DISPLAY ],
				'font-weight'    => '600',
				'letter-spacing' => '-0.01em',
			] ),
			'css_icon_boxdesc_wrappertitle_margin' => css( ITEM . ' .icon_box .desc_wrapper .title', 'margin', [ 'desktop' => dim( '14px', '', '12px', '' ) ] ),
			'css_icon_boxdesc_wrapperdesc_color'   => css( ITEM . ' .icon_box .desc_wrapper .desc', 'color', $C['inkSoft'] ),
			'css_icon_boxdesc_wrapperdesc_typography' => css( ITEM . ' .icon_box .desc_wrapper .desc', 'typography', [
				'desktop' => [ 'font-size' => '15px', 'line-height' => '1.6', 'font-family' => $F_TEXT ],
			] ),
		] ) ),
	];

	if ( $more ) {
		$items[] = text_item( $key . '-more',
			$link ? '<a href="' . $link . '">' . $more . ' &rarr;</a>' : $more,
			[
				'color'  => $more_muted ? $C['slate'] : $C['cyan'],
				'size'   => $more_muted ? '12.5px' : '14.5px',
				'weight' => $more_muted ? '500' : '600',
				'align'  => 'left',
				'title'  => 'More',
			]
		);
	}

	return $items;
}

/** Grid of feature cards, one wrap per card. */
function feature_cards( $key, array $cards, $size = '1/3' ) {
	$wraps = [];

	foreach ( $cards as $i => $c ) {
		$wraps[] = wrap( $key . '-w' . $i, $c['title'], $size, card_attr( '34px' ),
			feature_card( $key . '-c' . $i, $c['icon'], $c['title'], $c['desc'], $c['link'] ?? '', $c['more'] ?? '', ! empty( $c['muted'] ) ),
			$size === '1/3' ? '1/3' : $size, '1/1'
		);
	}

	return $wraps;
}

/* --------------------------------------------------------------- layouts */

/**
 * ".page-hero" — breadcrumbs, H1, lead and optional buttons over a soft glow.
 * $crumbs: list of [label, url|null]
 */
function page_hero( $key, array $crumbs, $h1, $lead, array $buttons = [] ) {
	global $C, $F_MONO;

	$crumb_html = [];
	foreach ( $crumbs as $c ) {
		$crumb_html[] = empty( $c[1] )
			? '<span style="color:#fff">' . $c[0] . '</span>'
			: '<a href="' . $c[1] . '">' . $c[0] . '</a>';
	}

	$items = [
		item( $key . '-crumbs', 'plain_text', 'Breadcrumbs', '1/1', [
			'content'              => '<span style="display:inline-flex;gap:8px;flex-wrap:wrap">' . implode( ' <span style="color:#54657a">/</span> ', $crumb_html ) . '</span>',
			'css_descdesca_color'  => css( ITEM . ' .desc', 'color', $C['slate'] ),
			'css_desc_links_color' => css( ITEM . ' .desc a', 'color', $C['inkSoft'] ),
			'css_desc_links_color_hover' => css( ITEM . ' .desc a:hover', 'color', $C['cyan'] ),
			'css_desc_typography'  => css( ITEM . ' .desc', 'typography', [
				'desktop' => [ 'font-size' => '12.5px', 'line-height' => '1.4', 'font-family' => $F_MONO ],
			] ),
		] ),
		heading_item( $key . '-h1', $h1, 'h1', '61px', '37px' ),
	];

	if ( $lead ) {
		$items[] = text_item( $key . '-lead', $lead, [ 'size' => '20.8px', 'lh' => '1.55', 'title' => 'Hero lead' ] );
	}

	$wraps = [
		wrap( $key . '-w', 'Hero copy', '1/1', [
			'css_advanced_flex' => css( WRP, 'width', [ 'desktop' => '900px', 'tablet' => '100%', 'mobile' => '100%' ] ),
		], $items ),
	];

	if ( $buttons ) {
		$btn_items = [];
		foreach ( $buttons as $i => $b ) {
			$btn_items[] = button_item( $key . '-btn' . $i, $b[0], $b[1], $b[2] ?? 'primary', '1/4', '', true );
		}
		$wraps[] = wrap( $key . '-wbtn', 'Hero buttons', '1/1', array_merge( btn_row(), [
			'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '14px', '', '0px', '' ) ] ),
		] ), $btn_items );
	}

	return section( $key, 'Page hero', [
		'css_advanced_padding'          => section_pad( '160px', '70px', '120px', '56px' ),
		'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
		'css_advanced_gradient'         => css( SEC, 'gradient', [
			'string' => 'radial-gradient(55% 70% at 88% 8%, rgba(25,198,218,0.16) 0%, rgba(124,108,240,0.12) 42%, rgba(7,15,28,0) 72%)',
		] ),
		'css_advanced_border_style'     => css( SEC, 'border-style', 'solid' ),
		'css_advanced_border_color'     => css( SEC, 'border-color', $C['lineSoft'] ),
		'css_advanced_border_width'     => css( SEC, 'border-width', [ 'desktop' => '0 0 1px 0' ] ),
	], $wraps );
}

/** ".cta-band" — the closing call-to-action band, same as the homepage. */
function cta_band( $key, $eyebrow, $h2, $lead, array $buttons, $bg = null ) {
	global $C;

	$items = [
		eyebrow( $key . '-eyebrow', $eyebrow, 'center' ),
		heading_item( $key . '-h2', $h2, 'h2', '48px', '32px', 'center' ),
		text_item( $key . '-lead', $lead, [ 'size' => '20.8px', 'lh' => '1.55', 'align' => 'center', 'title' => 'CTA lead', 'extra' => col_margin( '30px' ) ] ),
	];

	foreach ( $buttons as $i => $b ) {
		$items[] = button_item( $key . '-btn' . $i, $b[0], $b[1], $b[2] ?? 'primary', '1/4', '', true );
	}

	return section( $key, 'CTA band', [
		'css_advanced_padding'          => section_pad( '96px', '128px', '64px', '80px' ),
		'css_advanced_background_color' => css( SEC, 'background-color', $bg ?: $C['navy800'] ),
	], [
		wrap( $key . '-band', 'CTA band', '1/1', array_merge(
			card_attr( '72px', $C['surface2'], 'rgba(25,198,218,0.30)', '22px', false ),
			btn_row( 'center' ),
			[
				/* .cta-band{padding:72px 56px} + .inner{max-width:680px} */
				'css_advanced_padding'     => css( WRPI, 'padding', [
					/* full-width band, .inner{max-width:680px} reproduced by the side padding:
					   (1196 band - 680 text - 2x12 column gutter) / 2 = 246px */
					'desktop' => dim( '72px', '246px', '72px', '246px' ),
					'tablet'  => dim( '56px', '56px', '56px', '56px' ),
					'mobile'  => dim( '48px', '26px', '48px', '26px' ),
				] ),
				'css_advanced_align_items' => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
			]
		), $items ),
	] );
}

/** Framed image with the cyan glow used by .media-frame.glow */
function framed_image( $key, $url, $alt, $radius = '22px' ) {
	global $C;

	return item( $key, 'image', 'Image', '1/1', array_merge( col_margin( '20px' ), [
		'src'                     => $url,
		'size'                    => 'full',
		'alt'                     => $alt,
		'css_image_border_radius' => css( ITEM . ' .image_frame', 'border-radius', [ 'desktop' => "$radius $radius $radius $radius" ] ),
		'css_image_border_style'  => css( ITEM . ' .image_frame', 'border-style', 'solid' ),
		'css_image_border_color'  => css( ITEM . ' .image_frame', 'border-color', $C['line'] ),
		'css_image_border_width'  => css( ITEM . ' .image_frame', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_image_shadow'        => css( ITEM . ' .image_frame', 'box-shadow', '0 18px 50px -20px rgba(0,0,0,0.7)' ),
		'css_image_overflow'      => css( ITEM . ' .image_frame', 'overflow', 'hidden' ),
	] ) );
}

/**
 * ".app" cards as *items*, so they can live inside a half-width column.
 * BeBuilder nests section -> wrap -> item, so anything inside a column must be
 * an item; the card frame is drawn on the item's own .mcb-column-inner.
 */
function app_items( $key, array $rows ) {
	global $C;

	$items = [];

	foreach ( $rows as $i => $row ) {
		$html = '<span style="display:flex;gap:16px;align-items:flex-start">'
			. '<span style="flex:0 0 auto;font-family:\'JetBrains Mono\',monospace;font-size:13px;color:#19c6da;line-height:1.5">' . $row[0] . '</span>'
			. '<span style="display:block">'
			. '<b style="display:block;color:#fff;font-size:15.5px;margin-bottom:4px">' . $row[1] . '</b>'
			. '<span style="display:block;color:#aebdce;font-size:14.5px;line-height:1.55">' . $row[2] . '</span>'
			. '</span></span>';

		$items[] = text_item( $key . '-i' . $i, $html, [
			'title' => strip_tags( $row[1] ),
			'extra' => [
				'css_advanced_background_color' => css( ITEM . ' .mcb-column-inner', 'background-color', 'rgba(255,255,255,0.015)' ),
				'css_advanced_border_style'     => css( ITEM . ' .mcb-column-inner', 'border-style', 'solid' ),
				'css_advanced_border_color'     => css( ITEM . ' .mcb-column-inner', 'border-color', $C['line'] ),
				'css_advanced_border_width'     => css( ITEM . ' .mcb-column-inner', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
				'css_advanced_border_radius'    => css( ITEM . ' .mcb-column-inner', 'border-radius', [ 'desktop' => '14px 14px 14px 14px' ] ),
				'css_advanced_padding'          => css( ITEM . ' .mcb-column-inner', 'padding', [ 'desktop' => dim( '22px', '22px', '22px', '22px' ) ] ),
				'css_advanced_margin'           => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', '14px', '' ) ] ),
				'css_advanced_transition'       => css( ITEM . ' .mcb-column-inner', 'transition', '0.25s' ),
				'css_advanced_border_hover'     => css( ITEM . ' .mcb-column-inner:hover', 'border-color', 'rgba(25,198,218,0.35)' ),
				'css_advanced_background_hover' => css( ITEM . ' .mcb-column-inner:hover', 'background-color', 'rgba(25,198,218,0.04)' ),
			],
		] );
	}

	return $items;
}

/**
 * ".tl-item" timeline — a single left-aligned column with dot markers.
 * BeTheme's own `timeline` element only does the alternating layout, which does
 * not fit a half-width column, so the reference's simpler shape is rebuilt here.
 */
function timeline_items( $key, array $rows ) {
	global $C;

	$items = [];
	$last  = count( $rows ) - 1;

	foreach ( $rows as $i => $row ) {
		$border = $i === $last ? 'transparent' : 'rgba(255,255,255,0.10)';

		$html = '<span style="display:block;position:relative;padding:0 0 30px 26px;border-left:1px solid ' . $border . '">'
			. '<span style="position:absolute;left:-6px;top:5px;width:11px;height:11px;border-radius:50%;background:#19c6da;box-shadow:0 0 0 4px rgba(25,198,218,0.15)"></span>'
			. '<span style="display:block;font-family:\'JetBrains Mono\',monospace;font-size:12.5px;letter-spacing:0.06em;color:#19c6da;margin-bottom:4px">' . $row['date'] . '</span>'
			. '<b style="display:block;color:#fff;font-size:15.5px;margin-bottom:3px">' . $row['title'] . '</b>'
			. '<span style="display:block;color:#aebdce;font-size:14.5px;line-height:1.55">' . $row['content'] . '</span>'
			. '</span>';

		$items[] = text_item( $key . '-t' . $i, $html, [
			'title' => strip_tags( $row['title'] ),
			'extra' => [
				'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', '0px', '' ) ] ),
			],
		] );
	}

	return $items;
}

/**
 * A ".card" as a single item, so it can sit inside a column.
 * $icon is a Font Awesome class; $body may contain HTML.
 */
function card_item( $key, $icon, $title, $body ) {
	global $C;

	$html = '';

	if ( $icon ) {
		$html .= '<span style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:14px;background:rgba(25,198,218,0.10);border:1px solid rgba(255,255,255,0.10);margin-bottom:16px">'
			. '<i class="' . $icon . '" style="color:#19c6da;font-size:20px"></i></span>';
	}

	$html .= '<b style="display:block;font-family:\'Space Grotesk\',sans-serif;font-size:21px;font-weight:600;letter-spacing:-0.01em;color:#fff;margin-bottom:10px">' . $title . '</b>'
		. '<span style="display:block;color:#aebdce;font-size:15px;line-height:1.65">' . $body . '</span>';

	return text_item( $key, $html, [
		'title' => strip_tags( $title ),
		'extra' => [
			'css_advanced_background_color' => css( ITEM . ' .mcb-column-inner', 'background-color', $C['surface'] ),
			'css_advanced_border_style'     => css( ITEM . ' .mcb-column-inner', 'border-style', 'solid' ),
			'css_advanced_border_color'     => css( ITEM . ' .mcb-column-inner', 'border-color', $C['line'] ),
			'css_advanced_border_width'     => css( ITEM . ' .mcb-column-inner', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
			'css_advanced_border_radius'    => css( ITEM . ' .mcb-column-inner', 'border-radius', [ 'desktop' => '14px 14px 14px 14px' ] ),
			'css_advanced_padding'          => css( ITEM . ' .mcb-column-inner', 'padding', [ 'desktop' => dim( '28px', '28px', '28px', '28px' ) ] ),
			'css_advanced_margin'           => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', '20px', '' ) ] ),
			'css_advanced_transition'       => css( ITEM . ' .mcb-column-inner', 'transition', '0.28s cubic-bezier(0.2,0.7,0.2,1)' ),
			'css_advanced_border_hover'     => css( ITEM . ' .mcb-column-inner:hover', 'border-color', 'rgba(25,198,218,0.40)' ),
			'css_advanced_transform_hover'  => css( ITEM . ' .mcb-column-inner:hover', 'transform', 'translateY(-4px)' ),
			'css_advanced_shadow_hover'     => css( ITEM . ' .mcb-column-inner:hover', 'box-shadow', '0 18px 50px -20px rgba(0,0,0,0.7)' ),
		],
	] );
}

/**
 * ".stat" — a bordered card whose value is spectral-gradient clipped text.
 *   .stat{padding:28px;border:1px solid var(--line);border-radius:14px;background:rgba(255,255,255,.02)}
 *   .stat b{font-family:var(--f-display);font-size:clamp(2rem,3.4vw,2.9rem);letter-spacing:-.02em;
 *           background:var(--grad-spectral);background-clip:text;color:transparent}
 *   .stat span{color:var(--slate);font-size:13.5px;font-family:var(--f-mono);letter-spacing:.03em}
 * $value may contain HTML (e.g. the smaller "CHF " prefix).
 */
function stat_item( $key, $value, $caption ) {
	global $C, $F_DISPLAY, $F_MONO, $GRAD;

	return item( $key, 'quick_fact', strip_tags( $value ), '1/4', [
		'heading'     => $value,
		'heading_tag' => 'div',
		'title'       => $caption,
		'title_tag'   => 'div',
		'number'      => '',
		'align'       => 'left',

		'css_quick_fact_text_align'            => css( ITEM . ' .quick_fact', 'text-align', 'left' ),
		'css_quick_factheading_tag_typography' => css( ITEM . ' .quick_fact .heading_tag', 'typography', [
			'desktop'        => [ 'font-size' => '46.4px', 'line-height' => '1.1', 'font-family' => $F_DISPLAY ],
			'tablet'         => [ 'font-size' => '36px' ],
			'mobile'         => [ 'font-size' => '32px' ],
			'font-weight'    => '600',
			'letter-spacing' => '-0.02em',
		] ),
		/* gradient-clipped value */
		'css_stat_value_bg'    => css( ITEM . ' .quick_fact .heading_tag', 'background', $GRAD ),
		'css_stat_value_clip'  => css( ITEM . ' .quick_fact .heading_tag', '-webkit-background-clip', 'text' ),
		'css_stat_value_clip2' => css( ITEM . ' .quick_fact .heading_tag', 'background-clip', 'text' ),
		'css_stat_value_color' => css( ITEM . ' .quick_fact .heading_tag', 'color', 'transparent' ),
		'css_quick_factheading_tag_margin' => css( ITEM . ' .quick_fact .heading_tag', 'margin', [ 'desktop' => dim( '0px', '', '8px', '' ) ] ),

		'css_quick_facttitle_tag_color'      => css( ITEM . ' .quick_fact .title_tag', 'color', $C['slate'] ),
		'css_quick_facttitle_tag_typography' => css( ITEM . ' .quick_fact .title_tag', 'typography', [
			'desktop'        => [ 'font-size' => '13.5px', 'line-height' => '1.5', 'font-family' => $F_MONO ],
			'letter-spacing' => '0.03em',
		] ),
		'css_quick_facthr_narrow_width' => css( ITEM . ' .quick_fact .hr_narrow', 'width', '0px' ),

		/* the card frame lives on the item's own column so it can sit in a row of four */
		'css_advanced_background_color' => css( ITEM . ' .mcb-column-inner', 'background-color', 'rgba(255,255,255,0.02)' ),
		'css_advanced_border_style'     => css( ITEM . ' .mcb-column-inner', 'border-style', 'solid' ),
		'css_advanced_border_color'     => css( ITEM . ' .mcb-column-inner', 'border-color', $C['line'] ),
		'css_advanced_border_width'     => css( ITEM . ' .mcb-column-inner', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		'css_advanced_border_radius'    => css( ITEM . ' .mcb-column-inner', 'border-radius', [ 'desktop' => '14px 14px 14px 14px' ] ),
		'css_advanced_padding'          => css( ITEM . ' .mcb-column-inner', 'padding', [ 'desktop' => dim( '28px', '28px', '28px', '28px' ) ] ),
		'css_advanced_margin'           => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '12px', '24px', '12px' ) ] ),
	], '1/2', '1/1' );
}

/**
 * ".logos" strip — BeTheme's `clients` element, restyled to the reference:
 * a centred flex-wrap of bordered pills holding white logo marks.
 */
function clients_strip( $key ) {
	global $C;

	return item( $key, 'clients', 'Partner logos', '1/1', array_merge( col_margin( '0px' ), [
		'style'     => '',
		'size'      => 'be_clients',
		'orderby'   => 'menu_order',
		'order'     => 'ASC',
		'greyscale' => 0,

		/* .logos{display:flex;flex-wrap:wrap;justify-content:center;gap:18px 40px;opacity:.9} */
		'css_logos_display'  => css( ITEM . ' .clients_ul', 'display', 'flex' ),
		'css_logos_wrap'     => css( ITEM . ' .clients_ul', 'flex-wrap', 'wrap' ),
		'css_logos_justify'  => css( ITEM . ' .clients_ul', 'justify-content', 'center' ),
		'css_logos_align'    => css( ITEM . ' .clients_ul', 'align-items', 'center' ),
		'css_logos_gap'      => css( ITEM . ' .clients_ul', 'gap', '18px 40px' ),
		'css_logos_opacity'  => css( ITEM . ' .clients_ul', 'opacity', '0.9' ),
		'css_logos_li_width' => css( ITEM . ' .clients_ul li', 'width', 'auto' ),

		/* .logos .lg.mark{padding:8px 14px;border:1px solid var(--line);border-radius:999px} */
		'css_logos_pill_display' => css( ITEM . ' .clients_ul li .client_wrapper', 'display', 'inline-flex' ),
		'css_logos_pill_align'   => css( ITEM . ' .clients_ul li .client_wrapper', 'align-items', 'center' ),
		'css_logos_pill_pad'     => css( ITEM . ' .clients_ul li .client_wrapper', 'padding', '8px 14px' ),
		'css_logos_pill_border'  => css( ITEM . ' .clients_ul li .client_wrapper', 'border', '1px solid ' . $C['line'] ),
		'css_logos_pill_radius'  => css( ITEM . ' .clients_ul li .client_wrapper', 'border-radius', '999px' ),
		'css_logos_pill_height'  => css( ITEM . ' .clients_ul li .client_wrapper', 'height', 'auto' ),
		'css_logos_pill_bg'      => css( ITEM . ' .clients_ul li .client_wrapper', 'background', 'transparent' ),
		'css_logos_pill_trans'   => css( ITEM . ' .clients_ul li .client_wrapper', 'transition', '0.25s' ),
		'css_logos_pill_hover'   => css( ITEM . ' .clients_ul li .client_wrapper:hover', 'border-color', $C['cyan'] ),

		/* .logos .lg.mark img{max-height:34px;max-width:132px;filter:brightness(0) invert(1);opacity:.72} */
		'css_logos_img_h'        => css( ITEM . ' .clients_ul li img', 'height', 'auto' ),
		'css_logos_img_w'        => css( ITEM . ' .clients_ul li img', 'width', 'auto' ),
		'css_logos_img_maxh'     => css( ITEM . ' .clients_ul li img', 'max-height', '34px' ),
		'css_logos_img_maxw'     => css( ITEM . ' .clients_ul li img', 'max-width', '132px' ),
		'css_logos_img_filter'   => css( ITEM . ' .clients_ul li img', 'filter', 'brightness(0) invert(1)' ),
		'css_logos_img_opacity'  => css( ITEM . ' .clients_ul li img', 'opacity', '0.72' ),
		'css_logos_img_trans'    => css( ITEM . ' .clients_ul li img', 'transition', '0.25s' ),
		'css_logos_img_hover'    => css( ITEM . ' .clients_ul li:hover img', 'opacity', '1' ),
	] ) );
}

/**
 * ".imgcard" — the news card, shared by the homepage rail and the News page.
 *   .imgcard{border-radius:14px;overflow:hidden;border:1px solid var(--line);background:var(--surface)}
 *   .imgcard .ph{aspect-ratio:16/10;overflow:hidden}  .ph img{object-fit:cover;transition:.5s}
 *   .imgcard:hover{translateY(-4px);border-color:rgba(25,198,218,.35);box-shadow:var(--shadow)}
 *   .imgcard:hover .ph img{transform:scale(1.05)}
 *   .imgcard .bd{padding:24px}
 *   .imgcard .date{var(--f-mono);12px;letter-spacing:.05em;color:var(--cyan);margin-bottom:10px}
 *   .imgcard h3{1.15rem;line-height:1.25;margin-bottom:10px}
 *   .imgcard p{14.5px;color:var(--ink-soft)}
 *   .imgcard .more{var(--f-mono);12.5px;color:var(--cyan);margin-top:14px}
 */
function news_card( $key, $img, $alt, array $tags, $date, $title, $excerpt, $link, $size = '1/3', $img_h = '238px' ) {
	global $C, $F_MONO, $F_TEXT, $F_DISPLAY;

	$attr = card_attr( '0px' );
	unset( $attr['css_advanced_padding'] );

	/* the image sits flush against the card, so the card has to clip it */
	$attr['css_card_overflow']       = css( WRPI, 'overflow', 'hidden' );
	$attr['css_card_img_transition'] = css( WRPI . ' .image_frame .image_wrapper img', 'transition', '0.5s cubic-bezier(0.2,0.7,0.2,1)' );
	$attr['css_card_img_zoom']       = css( WRPI . ':hover .image_frame .image_wrapper img', 'transform', 'scale(1.05)' );
	$attr['css_card_border_hover']   = css( WRPI . ':hover', 'border-color', 'rgba(25,198,218,0.35)' );
	$attr['css_card_column']         = css( WRPI, 'flex-direction', 'column' );

	/* .bd{padding:24px} — applied to every item except the image. The column's own
	   12px side margins have to go, or the copy sits at 37px instead of 25px. */
	$bd = [
		'css_advanced_padding' => css( ITEM . ' .mcb-column-inner', 'padding', [ 'desktop' => dim( '0px', '24px', '0px', '24px' ) ] ),
		'css_advanced_margin'  => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '10px', '0px' ) ] ),
	];

	$items = [
		item( $key . '-img', 'image', 'Thumbnail', '1/1', array_merge( [
			/* flush to the card frame */
			'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '20px', '0px' ) ] ),
		], [
			'src'                    => $img,
			'size'                   => 'full',
			'alt'                    => $alt,
			'link'                   => $link,
			'link_type'              => 'custom',
			'image_height'           => 'custom',
			'css_image_cover_height' => css( ITEM . ' .image_frame.mfn-coverimg .image_wrapper img', 'height', [ 'desktop' => $img_h ] ),
			'css_image_frame_radius' => css( ITEM . ' .image_frame', 'border-radius', '0px' ),
			/* BeTheme shows a link/zoom overlay button on hover; the reference has none */
			'css_image_no_links'     => css( ITEM . ' .image_frame .image_wrapper .image_links', 'display', 'none' ),
			'css_image_no_mask'      => css( ITEM . ' .image_frame .image_wrapper .mask', 'display', 'none' ),
		] ) ),
	];

	if ( $tags ) {
		$tag_html = '<span style="display:flex;gap:8px;flex-wrap:wrap">';
		foreach ( $tags as $t ) {
			$tag_html .= '<span style="font-family:\'JetBrains Mono\',monospace;font-size:11px;letter-spacing:0.06em;color:#19c6da;'
				. 'border:1px solid rgba(25,198,218,0.3);background:rgba(25,198,218,0.07);padding:5px 11px;border-radius:999px">' . $t . '</span>';
		}
		$tag_html .= '</span>';

		$items[] = text_item( $key . '-tags', $tag_html, [
			'title' => 'Tags',
			'extra' => array_merge( $bd, [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '14px', '0px' ) ] ) ] ),
		] );
	}

	$items[] = text_item( $key . '-date', $date, [
		'color' => $C['cyan'], 'size' => '12px', 'title' => 'Date',
		'extra' => array_merge( $bd, [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '10px', '0px' ) ] ) ], [
			'css_date_font'   => css( ITEM . ' .desc', 'font-family', "'" . $F_MONO . "',monospace" ),
			'css_date_spacing'=> css( ITEM . ' .desc', 'letter-spacing', '0.05em' ),
		] ),
	] );

	$items[] = heading_item( $key . '-title', $title, 'h3', '18.4px', '18px', 'left', $link,
		array_merge( $bd, [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '10px', '0px' ) ] ) ], [
			'css_title_lh' => css( ITEM . ' .title', 'line-height', '1.25' ),
		] ) );

	$items[] = text_item( $key . '-desc', $excerpt, [
		'size' => '14.5px', 'lh' => '1.6', 'title' => 'Excerpt',
		'extra' => array_merge( $bd, [
			'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '14px', '0px' ) ] ),
			/* .imgcard p{flex:1} */
			'css_excerpt_grow'    => css( ITEM, 'flex-grow', '1' ),
		] ),
	] );

	$items[] = text_item( $key . '-more', '<a href="' . $link . '" style="display:inline-flex;align-items:center;gap:8px">Read <span aria-hidden="true">&rarr;</span></a>', [
		'color' => $C['cyan'], 'size' => '12.5px', 'title' => 'Read link',
		'extra' => array_merge( $bd, [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '24px', '0px' ) ] ) ], [
			'css_more_font' => css( ITEM . ' .desc', 'font-family', "'" . $F_MONO . "',monospace" ),
		] ),
	] );

	return wrap( $key . '-w', $title, $size, $attr, $items, $size, '1/1' );
}
