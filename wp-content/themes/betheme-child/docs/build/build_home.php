<?php
/**
 * Build the Miraex homepage as native BeBuilder (Muffin Builder) content.
 *
 * Source of truth for the content/design:
 * https://ashy-forest-0b7587303.7.azurestaticapps.net/  (index.html + css/app.css)
 *
 * Idempotent: uids are deterministic, so re-running rewrites the same page.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/lib_page.php';  /* stat_item(), clients_strip() */

/* ================================================================ CONTENT */

$sections = [];

/* ---------------------------------------------------------- 1. HERO ---- */

$hero_img = media( 'assets/img/hero-photonics.jpg' );

$hero_metrics = [
	[ 'hero-m1', 'MHz–100GHz+', 'conversion bandwidth' ],
	[ 'hero-m2', '10⁴',         'microwave↔optical energy gap bridged' ],
	[ 'hero-m3', '3',           'quantum verticals' ],
	[ 'hero-m4', 'EPFL',        'Innovation Park, CH' ],
];

$hero_metric_items = [];
foreach ( $hero_metrics as $m ) {
	$hero_metric_items[] = item( $m[0], 'quick_fact', $m[1], '1/4', array_merge( col_margin( '0px' ), [
		'width_switcher' => 'inline',
		'heading'     => $m[1],
		'heading_tag' => 'div',
		'title'       => $m[2],
		'title_tag'   => 'div',
		'number'      => '',
		'align'       => 'left',
		'css_quick_fact_text_align'                 => css( ITEM . ' .quick_fact', 'text-align', 'left' ),
		'css_quick_factheading_tag_color'           => css( ITEM . ' .quick_fact .heading_tag', 'color', $C['white'] ),
		'css_quick_factheading_tag_typography'      => css( ITEM . ' .quick_fact .heading_tag', 'typography', [
			'desktop'        => [ 'font-size' => '30px', 'line-height' => '1.1', 'font-family' => $F_DISPLAY ],
			'font-weight'    => '600',
			'letter-spacing' => '-0.02em',
		] ),
		'css_quick_factheading_tag_margin'          => css( ITEM . ' .quick_fact .heading_tag', 'margin', [ 'desktop' => dim( '0', '', '6px', '' ) ] ),
		'css_quick_facttitle_tag_color'             => css( ITEM . ' .quick_fact .title_tag', 'color', $C['slate'] ),
		'css_quick_facttitle_tag_typography'        => css( ITEM . ' .quick_fact .title_tag', 'typography', [
			'desktop'        => [ 'font-size' => '13px', 'line-height' => '1.45', 'font-family' => $F_MONO ],
			'letter-spacing' => '0.05em',
		] ),
		'css_quick_facthr_narrow_width'             => css( ITEM . ' .quick_fact .hr_narrow', 'width', '0px' ),
	] ), '1/2', '1/2' );
}

$sections[] = section( 'sec-hero', 'Hero — Sovereign quantum era', [
	/* .hero{min-height:100vh;display:flex;align-items:center} — the 120px belongs to
	   .hero-inner, so the section box runs to the bottom edge and the scroll cue can
	   sit 30px above it. */
	'css_advanced_padding'                            => css( SEC, 'padding', [
		'desktop' => dim( '0px', '', '0px', '' ),
		'mobile'  => dim( '0px', '0px', '0px', '0px' ),
	] ),
	'css_advanced_align_items'                        => css( SECW, 'align-items', [ 'desktop' => 'center' ] ),
	'css_advanced_background_color'                   => css( SEC, 'background-color', $C['navy900'] ),
	'css_advanced_background_image'                   => css( SEC, 'background-image', $hero_img['url'] ),
	'css_advanced_background_size'                    => css( SEC, 'background-size', 'cover' ),
	'css_advanced_background_position'                => css( SEC, 'background-position', 'center center' ),
	'css_advanced_background_repeat'                  => css( SEC, 'background-repeat', 'no-repeat' ),
	/* The reference stacks two gradients over the photo (.hero-media::after / ::before).
	   The vertical one ends in --navy-800 — the next section's colour — which is what
	   makes the hero flow into it with no visible seam. Both are set through `background`
	   rather than `background-image`, because mfnLocalStyle() wraps background-image
	   values in url(). */
	'css_advanced_background_overlay_gradients'       => css( SEC . ' .mcb-background-overlay', 'background',
		'linear-gradient(180deg,rgba(7,15,28,0.5),transparent 22%,transparent 70%,' . $C['navy800'] . '),'
		. 'linear-gradient(90deg,' . $C['navy900'] . ' 12%,rgba(7,15,28,0.62) 46%,rgba(7,15,28,0.15) 100%)' ),
	'css_advanced_background_overlay_opacity'         => css( SEC . ' .mcb-background-overlay', 'opacity', [ 'desktop' => '1' ] ),
	/* .hero{background:radial-gradient(120% 90% at 80% 30%,rgba(16,47,84,.6),var(--navy-900) 60%)} */
	'css_advanced_hero_radial'                        => css( SEC . ' .mcb-background-overlay', 'box-shadow', 'inset 0 0 200px 60px rgba(16,47,84,0.25)' ),
	'css_advanced_overflow'                           => css( SEC, 'overflow', 'hidden' ),
	'css_advanced_min_height'                         => css( SEC, 'min-height', '100vh' ),
], [
	wrap( 'hero-w1', 'Hero copy', '1/1', [
		'css_advanced_flex'    => css( WRP, 'width', [ 'desktop' => '784px', 'tablet' => '100%', 'mobile' => '100%' ] ),
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '120px', '', '0px', '' ), 'mobile' => dim( '100px', '', '0px', '' ) ] ),
	], [
		/* .parent-strip{display:inline-flex;gap:12px;padding:8px 8px 8px 16px;border:1px solid var(--line);
		   border-radius:999px;background:rgba(255,255,255,.03);font-size:13px;color:var(--ink-soft);margin-bottom:30px}
		   .parent-strip b{color:#fff;font-weight:600}
		   .parent-strip .tag{font-family:var(--f-mono);font-size:11px;color:#04121b;background:var(--cyan);
		   padding:5px 11px;border-radius:999px;letter-spacing:.04em} */
		text_item( 'hero-strip',
			'<span style="display:inline-flex;align-items:center;gap:12px;padding:8px 8px 8px 16px;border:1px solid rgba(255,255,255,0.10);border-radius:999px;background:rgba(255,255,255,0.03);font-size:13px;line-height:1.65;color:#aebdce">'
			. 'Now part of <b style="color:#fff;font-weight:600">SEALSQ</b>'
			. '<span style="font-family:\'JetBrains Mono\',monospace;font-size:11px;letter-spacing:0.04em;color:#04121b;background:#19c6da;padding:5px 11px;border-radius:999px;white-space:nowrap">Quantum Sovereign Stack</span>'
			. '</span>',
			[ 'title' => 'SEALSQ strip', 'extra' => col_margin( '30px' ) ]
		),
		heading_item( 'hero-h1', 'The quantum interconnect layer of the <span style="background:' . $GRAD . ';-webkit-background-clip:text;background-clip:text;color:transparent">sovereign quantum era</span>.', 'h1', '83px', '43px', 'left', '', array_merge( col_margin( '24px' ), [
			'css_letterspacing' => css( ITEM . ' .title', 'letter-spacing', '-0.035em' ),
		] ) ),
		text_item( 'hero-lead', 'Miraex builds thin-film lithium tantalate photonic integrated circuits that convert quantum information between microwave and optical light — the connective tissue linking quantum processors, sensors and networks into one coherent infrastructure.', [
			'size' => '20.8px', 'lh' => '1.55', 'title' => 'Hero lead',
			'extra' => array_merge( col_margin( '36px' ), [
				'css_lead_maxwidth' => css( ITEM . ' .mcb-column-inner', 'max-width', '624px' ),
			] ),
		] ),
	] ),
	wrap( 'hero-w2', 'Hero buttons', '1/1', array_merge( btn_row(), [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '14px', '', '30px', '' ) ] ),
	] ), [
		button_item( 'hero-btn1', 'Explore the platform', '/technology/', 'primary', '1/4', '', true ),
		button_item( 'hero-btn2', 'Request a datasheet', '/contact/', 'ghost', '1/4', '', true ),
	] ),
	/* .hero-meta{display:flex;gap:30px;flex-wrap:wrap;margin-top:54px;padding-top:30px;
	   border-top:1px solid var(--line-soft)} — inside the 760px hero column, so the
	   four metrics wrap 3 + 1 exactly like the reference. */
	wrap( 'hero-w3', 'Hero metrics', '1/1', [
		'css_advanced_flex'         => css( WRP, 'width', [ 'desktop' => '784px', 'tablet' => '100%', 'mobile' => '100%' ] ),
		'css_advanced_padding'      => css( WRPI, 'padding', [ 'desktop' => dim( '30px', '', '0px', '' ) ] ),
		'css_advanced_margin'       => css( WRPI, 'margin', [ 'desktop' => dim( '24px', '', '0px', '' ) ] ),
		'css_advanced_border_style' => css( WRPI, 'border-style', 'solid' ),
		'css_advanced_border_color' => css( WRPI, 'border-color', $C['lineSoft'] ),
		'css_advanced_border_width' => css( WRPI, 'border-width', [ 'desktop' => '1px 0 0 0' ] ),
		'css_advanced_flex_wrap'    => css( WRPI, 'flex-wrap', [ 'desktop' => 'wrap' ] ),
		'css_advanced_gap'          => css( WRPI, 'gap', '18px 30px' ),
		'css_advanced_pad_bottom'   => css( WRPI, 'padding-bottom', '120px' ),
	], $hero_metric_items ),

	/* The child theme already ships the reference rules for this, including the
	   animated dot (@keyframes cue), so reuse the original markup instead of
	   restyling it: .miraex-page .scroll-cue / .scroll-cue .dot in miraex.css.
	   `.scroll-cue` is absolute, so the positioned ancestors BeBuilder adds
	   (.mcb-column-inner and .mcb-wrap-inner are both position:relative) have to be
	   neutralised for it to anchor to .section_wrapper — i.e. the hero itself. */
	wrap( 'hero-cue', 'Scroll cue', '1/1', [
		'css_advanced_position' => css( WRPI, 'position', 'static' ),
	], [
		text_item( 'hero-cue-text',
			'<div class="scroll-cue"><span>Scroll</span><span class="dot"></span></div>',
			[
				'title' => 'Scroll cue',
				'extra' => array_merge( col_margin( '0px' ), [
					'css_cue_static' => css( ITEM . ' .mcb-column-inner', 'position', 'static' ),
				] ),
			]
		),
	] ),
] );

/* ------------------------------------------------- 2. AREA OF EXPERTISE - */

$verticals = [
	[
		'key'   => 'vert-computing',
		'num'   => '01 — Computing',
		'icon'  => 'fas fa-microchip',
		'title' => 'Distributed Quantum Computing',
		'desc'  => 'Quantum interconnects bridge stationary microwave qubits and flying optical photons, linking distant QPUs into one cluster over telecom fibre.',
		'link'  => '/distributed-quantum-computing/',
	],
	[
		'key'   => 'vert-sensing',
		'num'   => '02 — Sensing',
		'icon'  => 'fas fa-satellite-dish',
		'title' => 'Quantum Sensing',
		'desc'  => 'Ultra-low-noise transducers and entanglement-based distributed sensing for precision navigation, geophysics and defence.',
		'link'  => '/quantum-sensing/',
	],
	[
		'key'   => 'vert-networking',
		'num'   => '03 — Networking',
		'icon'  => 'fas fa-project-diagram',
		'title' => 'Quantum Networking',
		'desc'  => 'Quantum repeaters that extend entanglement beyond the fibre limit — the backbone of a future, planet-scale quantum internet.',
		'link'  => '/quantum-networking/',
	],
];

$vertical_wraps = [];
foreach ( $verticals as $v ) {
	$card = card_attr( '30px' );
	$card['css_advanced_flex_direction'] = css( WRPI, 'flex-direction', 'column' );
	$card['css_advanced_min_height']     = css( WRPI, 'min-height', '340px' );

	$vertical_wraps[] = wrap( $v['key'] . '-wrap', $v['title'], '1/3', $card, [
		text_item( $v['key'] . '-num', $v['num'], [
			'color' => $C['slate'], 'size' => '12px', 'weight' => '500', 'title' => 'Card label',
			'extra' => [
				/* .vert-card .num{margin-bottom:auto} pushes the rest to the card foot */
				'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', 'auto', '' ) ] ),
			],
		] ),
		item( $v['key'] . '-box', 'icon_box', $v['title'], '1/1', array_merge( col_margin( '10px' ), [
			'title'         => $v['title'],
			'title_tag'     => 'h3',
			'content'       => $v['desc'],
			'icon'          => $v['icon'],
			'icon_position' => 'top',
			'link'          => $v['link'],
			'border'        => 0,
			'css_icon_boxicon_wrappericon_color'          => css( ITEM . ' .icon_box .icon_wrapper .icon', 'color', $C['cyan'] ),
			'css_icon_boxicon_wrapperi_font_size'         => css( ITEM . ' .icon_box .icon_wrapper i', 'font-size', [ 'desktop' => '24px' ] ),
			'css_icon_boxicon_wrapper_background'         => css( ITEM . ' .icon_box .icon_wrapper', 'background', 'rgba(25,198,218,0.10)' ),
			'css_icon_boxicon_wrapper_border_style'       => css( ITEM . ' .icon_box .icon_wrapper', 'border-style', 'solid' ),
			'css_icon_boxicon_wrapper_border_color'       => css( ITEM . ' .icon_box .icon_wrapper', 'border-color', $C['line'] ),
			'css_icon_boxicon_wrapper_border_width'       => css( ITEM . ' .icon_box .icon_wrapper', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
			'css_icon_boxicon_wrapper_border_radius'      => css( ITEM . ' .icon_box .icon_wrapper', 'border-radius', [ 'desktop' => '13px 13px 13px 13px' ] ),
			'css_icon_boxicon_wrapper_width'              => css( ITEM . ' .icon_box .icon_wrapper', 'width', '52px' ),
			'css_icon_boxicon_wrapper_height'             => css( ITEM . ' .icon_box .icon_wrapper', 'height', '52px' ),
			'css_icon_boxicon_wrapper_lineheight'         => css( ITEM . ' .icon_box .icon_wrapper', 'line-height', '1' ),
			'css_icon_boxicon_wrapper_fontsize'         => css( ITEM . ' .icon_box .icon_wrapper', 'font-size', '24px' ),
			'css_icon_boxicon_wrapper_margin'             => css( ITEM . ' .icon_box .icon_wrapper', 'margin', [ 'desktop' => dim( '0px', '0px', '20px', '0px' ) ] ),
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
			'css_icon_boxdesc_wrappertitle_color'         => css( ITEM . ' .icon_box .desc_wrapper .title', 'color', $C['white'] ),
			'css_icon_boxdesc_wrappertitle_typography'    => css( ITEM . ' .icon_box .desc_wrapper .title', 'typography', [
				'desktop'        => [ 'font-size' => '23px', 'line-height' => '1.2', 'font-family' => $F_DISPLAY ],
				'font-weight'    => '600',
				'letter-spacing' => '-0.01em',
			] ),
			'css_icon_boxdesc_wrappertitle_margin'        => css( ITEM . ' .icon_box .desc_wrapper .title', 'margin', [ 'desktop' => dim( '14px', '', '12px', '' ) ] ),
			'css_icon_boxdesc_wrapperdesc_color'          => css( ITEM . ' .icon_box .desc_wrapper .desc', 'color', $C['inkSoft'] ),
			'css_icon_boxdesc_wrapperdesc_typography'     => css( ITEM . ' .icon_box .desc_wrapper .desc', 'typography', [
				'desktop' => [ 'font-size' => '15.5px', 'line-height' => '1.6', 'font-family' => $F_TEXT ],
			] ),
		] ) ),
		text_item( $v['key'] . '-more', '<a href="' . $v['link'] . '">Explore &rarr;</a>', [
			'color' => $C['cyan'], 'size' => '14.5px', 'weight' => '600', 'title' => 'Explore link',
		] ),
	], '1/3', '1/1' );
}

$sections[] = section( 'sec-expertise', 'Area of expertise', [
	'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
], array_merge( [
	wrap( 'exp-head', 'Section head', '1/1', [
		/* .section-head{max-width:760px;margin-bottom:48px} centred — padding keeps the
		   wrap on its own flex line so the first card cannot slide in beside it. */
		'css_advanced_padding' => css( WRPI, 'padding', [
			'desktop' => dim( '0px', '18%', '48px', '18%' ),
			'mobile'  => dim( '0px', '0px', '32px', '0px' ),
		] ),
	], [
		eyebrow( 'exp-eyebrow', 'Our area of expertise', 'center' ),
		heading_item( 'exp-h2', 'Connecting quantum resources across<br>frequency, distance and modality', 'h2', '48px', '32px', 'center' ),
		text_item( 'exp-lead', 'Photonic integrated circuits deliver a genuine quantum advantage through interconnectivity and entanglement — the foundation distributed sensing, computing and networks are built on.', [
			'size' => '20.8px', 'lh' => '1.55', 'align' => 'center', 'title' => 'Section lead',
		] ),
	] ),
], $vertical_wraps ) );

/* ------------------------------------------------- 3. CORE CAPABILITY --- */

$ticks = [
	[ 'tick1', '<strong>High-efficiency</strong> direct electro-optic transduction, MHz to beyond 100 GHz' ],
	[ 'tick2', '<strong>Cryogenic &amp; quantum-device compatible</strong> — operates alongside superconducting processors' ],
	[ 'tick3', '<strong>Ultra-low-noise</strong> microwave detection to ~hundreds of photons' ],
	[ 'tick4', '<strong>Entanglement</strong> generated between the microwave and optical domains' ],
];

$tick_items = [];
foreach ( $ticks as $t ) {
	$tick_items[] = item( $t[0], 'list', 'Tick', '1/1', array_merge( col_margin( '2px' ), [
		'icon'      => 'fas fa-check',
		'title'     => '',
		'title_tag' => 'h4',
		'content'   => $t[1],
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

$journey_img = media( 'assets/img/photon-journey.jpg' );

$sections[] = section( 'sec-core', 'Core capability — microwave in, optical out', [
	'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
	'css_advanced_align_items'      => css( SECW, 'align-items', [ 'desktop' => 'center' ] ),
], [
	wrap( 'core-left', 'Copy', '1/2', [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0', '40px', '0', '0' ) ] ),
	], array_merge( [
		eyebrow( 'core-eyebrow', 'The core capability' ),
		heading_item( 'core-h2', 'Microwave in. Optical out.<br>A bridge across five orders of magnitude.', 'h2', '48px', '32px' ),
		text_item( 'core-lead', 'The energies of stationary qubits (microwave photons) and flying qubits (optical photons) differ by roughly 10⁴. Miraex\'s traveling-wave electro-optic transducers convert coherently between the two domains — without which distributed quantum architectures cannot function at scale.', [
			'size' => '20.8px', 'lh' => '1.55', 'title' => 'Core lead',
		] ),
	], $tick_items, [
		button_item( 'core-btn', 'Inside the TFLT platform', '/technology/', 'ghost', '1/2', '', false,
			array_merge( col_margin( '0px', '28px' ), [ 'icon' => 'fas fa-arrow-right' ] ) ),
	] ) ),
	wrap( 'core-right', 'Image', '1/2', [], [
		item( 'core-img', 'image', 'Photon journey', '1/1', array_merge( col_margin( '0px' ), [
			'src'                     => $journey_img['url'],
			'size'                    => 'full',
			'alt'                     => 'Microwave waves entering a photonic chip on the left and emerging as an optical beam on the right',
			'lazy_load'               => '',
			'css_image_border_radius' => css( ITEM . ' .image_frame', 'border-radius', [ 'desktop' => '18px 18px 18px 18px' ] ),
			'css_image_border_style'  => css( ITEM . ' .image_frame', 'border-style', 'solid' ),
			'css_image_border_color'  => css( ITEM . ' .image_frame', 'border-color', $C['line'] ),
			'css_image_border_width'  => css( ITEM . ' .image_frame', 'border-width', [ 'desktop' => '1px 1px 1px 1px' ] ),
		] ) ),
	] ),
] );

/* --------------------------------------------------- 4. ROOT-TO-QUBIT --- */

$stack = [
	[ 'stk5', '05', 'Orbital infrastructure', 'Quantum-secure links across Low Earth Orbit — the Quantum Orbital Space Cloud (QOSC)', 'WISeSat · QOSC', false ],
	[ 'stk4', '04', 'Quantum networking & QKD', 'Entanglement distribution and key delivery at planetary scale', 'Quantum internet', false ],
	[ 'stk3', '03', 'Quantum interconnect — the missing link', 'TFLT PIC microwave-to-optical transduction bridging processors, sensors and networks', 'Miraex', true ],
	[ 'stk2', '02', 'Quantum processors', 'Qubit hardware that the interconnect links into collective compute', 'EeroQ · ColibriTD', false ],
	[ 'stk1', '01', 'Post-quantum silicon', 'Quantum-resistant secure microcontrollers & root of trust', 'SEALSQ QS7001', false ],
];

$flush = [ 'css_advanced_margin' => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '', '0px', '' ) ] ) ];

$stack_wraps = [];
foreach ( $stack as $s ) {
	list( $key, $num, $title, $desc, $who, $is_miraex ) = $s;

	/* .stack-layer{display:grid;grid-template-columns:54px 1fr auto;align-items:center;
	   gap:20px;padding:20px 24px;border-radius:14px;background:var(--surface)}
	   .stack-layer.is-miraex{background:linear-gradient(100deg,rgba(25,198,218,.14),rgba(124,108,240,.12));
	   border-color:rgba(25,198,218,.5)} + a 3px --grad-spectral bar down the left edge,
	   which is a second background layer here since ::before is not reachable. */
	$attr = card_attr( '20px', $C['surface'], $is_miraex ? 'rgba(25,198,218,0.5)' : $C['line'], '14px' );
	/* these belong to equal-height card grids; on a full-width row the forced
	   height:100% swallows the 12px gap between rows */
	unset( $attr['css_advanced_gutter'], $attr['css_advanced_height'], $attr['css_advanced_align_self'] );

	$attr['css_advanced_padding']     = css( WRPI, 'padding', [ 'desktop' => dim( '20px', '24px', '20px', '24px' ) ] );
	$attr['css_advanced_margin']      = css( WRPI, 'margin', [ 'desktop' => dim( '0px', '', '12px', '' ) ] );
	$attr['css_advanced_align_items'] = css( WRPI, 'align-items', [ 'desktop' => 'center' ] );
	$attr['css_advanced_gap']         = css( WRPI, 'gap', '20px' );
	$attr['css_advanced_overflow']    = css( WRPI, 'overflow', 'hidden' );
	$attr['css_advanced_transition']  = css( WRPI, 'transition', '0.3s cubic-bezier(0.2,0.7,0.2,1)' );
	$attr['css_advanced_border_hover']     = css( WRPI . ':hover', 'border-color', 'rgba(25,198,218,0.45)' );
	$attr['css_advanced_background_hover'] = css( WRPI . ':hover', 'background-color', $C['surface2'] );
	$attr['css_advanced_transform_hover']  = css( WRPI . ':hover', 'transform', 'translateX(6px)' );

	if ( $is_miraex ) {
		$attr['css_advanced_background_layers'] = css( WRPI, 'background',
			'linear-gradient(180deg,#19c6da,#3b82f6 48%,#7c6cf0) left center/3px 100% no-repeat,'
			. 'linear-gradient(100deg,rgba(25,198,218,0.14),rgba(124,108,240,0.12))' );
	}

	/* 54px | 1fr | auto — flex-grow/shrink/basis separately, because mfnLocalStyle
	   prefixes the `flex` shorthand with "0 0 ". */
	$fixed = function( $basis ) {
		return [
			'css_advanced_margin'      => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
			'css_stack_col_width'      => css( ITEM, 'width', $basis ),
			'css_stack_col_grow'       => css( ITEM, 'flex-grow', '0' ),
			'css_stack_col_shrink'     => css( ITEM, 'flex-shrink', '0' ),
			'css_stack_col_basis'      => css( ITEM, 'flex-basis', $basis ),
		];
	};

	$fluid = [
		'css_advanced_margin'  => css( ITEM . ' .mcb-column-inner', 'margin', [ 'desktop' => dim( '0px', '0px', '0px', '0px' ) ] ),
		'css_stack_col_width'  => css( ITEM, 'width', 'auto' ),
		'css_stack_col_grow'   => css( ITEM, 'flex-grow', '1' ),
		'css_stack_col_shrink' => css( ITEM, 'flex-shrink', '1' ),
		'css_stack_col_basis'  => css( ITEM, 'flex-basis', 'auto' ),
	];

	$pill = $is_miraex
		? 'font-family:\'JetBrains Mono\',monospace;font-size:12px;color:#04121b;background:#19c6da;border:1px solid transparent;font-weight:600'
		: 'font-family:\'JetBrains Mono\',monospace;font-size:12px;color:#aebdce;border:1px solid rgba(255,255,255,0.10)';

	$stack_wraps[] = wrap( $key . '-wrap', $num . ' ' . $title, '1/1', $attr, [
		/* .stack-layer .lyr-n{font-family:var(--f-mono);font-size:13px;color:var(--slate)} */
		text_item( $key . '-num', $num, [
			'color'  => $is_miraex ? $C['cyan'] : $C['slate'],
			'size'   => '13px',
			'weight' => '500',
			'title'  => 'Layer number',
			'extra'  => array_merge( $fixed( '54px' ), [
				'css_stack_num_font' => css( ITEM . ' .desc', 'font-family', "'JetBrains Mono',monospace" ),
			] ),
		] ),
		/* .stack-layer h4{font-size:1.1rem;margin-bottom:3px} · p{font-size:14px} */
		text_item( $key . '-body',
			'<h4 style="margin:0 0 3px;font-family:\'Space Grotesk\',sans-serif;font-size:17.6px;line-height:1.15;font-weight:600;letter-spacing:-0.01em;color:'
			. ( $is_miraex ? '#34e2f0' : '#ffffff' ) . '">' . $title . '</h4>'
			. '<p style="margin:0;font-size:14px;line-height:1.55;color:#aebdce">' . $desc . '</p>',
			[ 'title' => 'Layer body', 'extra' => $fluid ]
		),
		/* .stack-layer .who — a bordered pill; filled cyan on the Miraex row */
		text_item( $key . '-who',
			'<span style="display:inline-block;' . $pill . ';padding:7px 13px;border-radius:999px;white-space:nowrap">' . $who . '</span>',
			[ 'align' => 'right', 'title' => 'Layer owner', 'extra' => $fixed( 'auto' ) ]
		),
	] );
}

$sections[] = section( 'sec-stack', 'Root-to-Qubit stack', [
	'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
], array_merge( [
	wrap( 'stack-head', 'Section head', '1/1', [
		'css_advanced_padding' => css( WRPI, 'padding', [
			'desktop' => dim( '0px', '36%', '44px', '' ),
			'mobile'  => dim( '0px', '0px', '32px', '0px' ),
		] ),
	], [
		eyebrow( 'stack-eyebrow', 'Where Miraex sits' ),
		heading_item( 'stack-h2', 'The Root-to-Qubit stack', 'h2', '48px', '32px' ),
		text_item( 'stack-lead', 'SEALSQ assembled a sovereign, end-to-end quantum architecture — from post-quantum silicon to orbit. Miraex closes the layer that makes it whole: the quantum interconnect.', [
			'size' => '20.8px', 'lh' => '1.55', 'title' => 'Section lead',
		] ),
	] ),
], $stack_wraps, [
	wrap( 'stack-note', 'Note', '1/1', [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '18px', '', '0', '' ) ] ),
	], [
		text_item( 'stack-note-text', 'Root-to-Qubit is SEALSQ\'s architecture spanning quantum-resistant semiconductors to orbital infrastructure. Miraex completes the interconnect layer.', [
			'color' => $C['slate'], 'size' => '14px', 'title' => 'Note',
		] ),
	] ),
] ) );

/* --------------------------------------------------------- 5. PROOF ----- */

$stats = [
	[ 'stat1', '10',   'Photonics &amp; quantum specialists' ],
	[ 'stat2', '<span style="font-size:0.6em;letter-spacing:0.02em">CHF </span>2.4M', 'Innosuisse innovation grant' ],
	[ 'stat3', 'EPFL', 'Innovation Park, Lake Geneva' ],
	[ 'stat4', 'LAES', 'SEALSQ-backed (NASDAQ)' ],
];

$stat_items = [];
foreach ( $stats as $st ) {
	$stat_items[] = stat_item( $st[0], $st[1], $st[2] );
}

$sections[] = section( 'sec-proof', 'Proof — stats & recognition', [
	/* .section-sm{padding-block:var(--s6)} = 64px */
	'css_advanced_padding'          => section_pad( '64px', '64px', '56px', '56px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
], [
	wrap( 'proof-stats', 'Stats', '1/1', [], $stat_items ),
	wrap( 'proof-head', 'Logos head', '1/1', [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '64px', '', '26px', '' ) ] ),
	], [
		eyebrow( 'proof-eyebrow', 'Backed & recognised by', 'center' ),
	] ),
	wrap( 'proof-logos', 'Logos', '1/1', [], [ clients_strip( 'proof-clients' ) ] ),
] );

/* ---------------------------------------------------------- 6. NEWS ----- */

$news = [
	[
		'key'   => 'news1',
		'img'   => 'assets/img/orbital-qkd.jpg',
		'alt'   => 'Satellite quantum link over Earth',
		'date'  => '02 June 2026',
		'title' => 'SEALSQ acquires 100% of Miraex, completing its Quantum Sovereign Vertical Stack',
		'desc'  => 'The acquisition closes the quantum interconnect layer and anchors the Quantum Orbital Space Cloud.',
		'link'  => '/news/sealsq-acquires-miraex/',
	],
	[
		'key'   => 'news2',
		'img'   => 'assets/img/cleanroom.jpg',
		'alt'   => 'Cleanroom fabrication',
		'date'  => '07 April 2026',
		'title' => 'Q-Modus SNSF Bridge: cryogenic TFLT modulator chips with EPFL',
		'desc'  => 'Thin-film lithium tantalate modulators developed with Prof. Villanueva\'s lab and Swiss partners.',
		'link'  => '/news/q-modus-snsf-bridge/',
	],
	[
		'key'   => 'news3',
		'img'   => 'assets/img/cryostat.jpg',
		'alt'   => 'Quantum hardware',
		'date'  => '26 February 2025',
		'title' => 'Miraex joins the Swiss National Startup Team in Silicon Valley',
		'desc'  => 'Selected from a record 200 applicants for the Venture Leaders Technology roadshow.',
		'link'  => '/news/venture-leaders-technology/',
	],
];

$news_wraps = [];
foreach ( $news as $n ) {
	$img = media( $n['img'] );
	$news_wraps[] = news_card( $n['key'], $img['url'], $n['alt'], [], $n['date'], $n['title'], $n['desc'], $n['link'] );
}

/* head and cards are two sections so the cards alone can become a swipe rail on
   phones — a scroll container cannot hold only some of its flex children. */
$sections[] = section( 'sec-news', 'Latest news', [
	'css_advanced_padding'          => section_pad( '128px', '0px', '80px', '0px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
], [
	wrap( 'news-head', 'Section head', '2/3', [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '', '44px', '' ) ] ),
		'css_advanced_flex'    => css( WRP, 'width', [ 'desktop' => '584px', 'tablet' => '100%', 'mobile' => '100%' ] ),
		'css_advanced_flex_grow' => css( WRP, 'flex-grow', [ 'desktop' => '1' ] ),
	], [
		eyebrow( 'news-eyebrow', 'Latest news' ),
		heading_item( 'news-h2', 'Signals from the lab and the group', 'h2', '48px', '32px' ),
	] ),
	wrap( 'news-cta', 'All news', '1/3', [
		'css_advanced_justify_content' => css( WRPI, 'justify-content', [ 'desktop' => 'flex-end' ] ),
		'css_advanced_align_items'     => css( WRPI, 'align-items', [ 'desktop' => 'flex-end' ] ),
		'css_advanced_padding'         => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '', '44px', '' ) ] ),
	], [
		button_item( 'news-btn', 'All news', '/news/', 'ghost', '1/1', 'right', false,
			[ 'icon' => 'fas fa-arrow-right' ] ),
	] ),
] );

$sections[] = section( 'sec-news-cards', 'Latest news — cards', array_merge( [
	'css_advanced_padding'          => section_pad( '0px', '128px', '0px', '80px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
], mobile_rail() ), $news_wraps );

/* ----------------------------------------------------------- 7. CTA ----- */

$sections[] = section( 'sec-cta', 'CTA — let’s connect', [
	'css_advanced_padding'          => section_pad( '0', '128px', '0', '80px' ),
	'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
], [
	wrap( 'cta-band', 'CTA band', '1/1', array_merge(
		card_attr( '72px', $C['surface2'], 'rgba(25,198,218,0.30)', '22px', false ),
		btn_row( 'center' ),
		[
			'css_advanced_padding'     => css( WRPI, 'padding', [
				/* full-width band, .inner{max-width:680px} reproduced by the side padding:
				   (1196 band - 680 text - 2x12 column gutter) / 2 = 246px */
				'desktop' => dim( '72px', '246px', '72px', '246px' ),
				'tablet'  => dim( '56px', '56px', '56px', '56px' ),
				'mobile'  => dim( '48px', '26px', '48px', '26px' ),
			] ),
			'css_advanced_align_items' => css( WRPI, 'align-items', [ 'desktop' => 'center' ] ),
		]
	), [
		eyebrow( 'cta-eyebrow', 'Let’s connect', 'center' ),
		heading_item( 'cta-h2', 'Interested in our TFLT PIC platform for quantum interconnectivity?', 'h2', '48px', '32px', 'center' ),
		text_item( 'cta-lead', 'Tell us about your processor, payload or network. We’ll route you to the right engineer — and, where it fits, to the wider SEALSQ quantum stack.', [
			'size' => '20.8px', 'lh' => '1.55', 'align' => 'center', 'title' => 'CTA lead',
		] ),
		button_item( 'cta-btn1', 'Request a datasheet', '/contact/', 'primary', '1/4', '', true ),
		button_item( 'cta-btn2', 'Browse resources', '/resources/', 'ghost', '1/4', '', true ),
	] ),
] );

/* ============================================================ PERSISTENCE */

/* 1. the page */

$page = get_page_by_path( 'home' );

if ( ! $page ) {
	$page_id = wp_insert_post([
		'post_type'    => 'page',
		'post_title'   => 'Home',
		'post_name'    => 'home',
		'post_status'  => 'publish',
		'post_content' => '',
		'post_author'  => 1,
	]);
} else {
	$page_id = $page->ID;
}

if ( is_wp_error( $page_id ) || ! $page_id ) {
	fwrite( STDERR, "Could not create the page\n" );
	exit( 1 );
}

/* 2. builder content + regenerated local styles */

$flat_count = mfn_store_template( $page_id, $sections );

update_post_meta( $page_id, 'mfn-post-hide-title', '1' );
update_post_meta( $page_id, 'mfn-post-remove-padding', '1' );

/* 5. make it the front page */

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_id );

printf(
	"page_id=%d sections=%d flat_nodes=%d css=%s\n",
	$page_id,
	count( $sections ),
	$flat_count,
	wp_upload_dir()['basedir'] . '/betheme/css/post-' . $page_id . '.css'
);
