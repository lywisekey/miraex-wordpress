<?php
/**
 * Build every inner page of the Miraex site as native BeBuilder content,
 * from html-redesign/*.html.
 *
 * Pages built (parents first):
 *   technology · distributed-quantum-computing · quantum-sensing · quantum-networking
 *   about · news (+ 3 child articles) · resources · careers · contact
 *
 * Idempotent — matched by slug, uids derived from stable keys.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';
require_once __DIR__ . '/lib_page.php';
require_once __DIR__ . '/lib_hubspot.php';

$IMG = [
	'chip'      => media( 'assets/img/pic-chip.jpg' ),
	'cryostat'  => media( 'assets/img/cryostat.jpg' ),
	'journey'   => media( 'assets/img/photon-journey.jpg' ),
	'orbital'   => media( 'assets/img/orbital-qkd.jpg' ),
	'cleanroom' => media( 'assets/img/cleanroom.jpg' ),
	'hero'      => media( 'assets/img/hero-photonics.jpg' ),
];

$pages = [];

/* ==================================================== 1. TECHNOLOGY ==== */


/* ====================================================== LEGAL PAGES ==== */

/**
 * Privacy / terms: a hero plus one 820px column of headings and prose, the same
 * measure as a news article.
 *
 * $blocks is a flat list of [ 'h'|'p'|'ul', content ]. Anything the company still has
 * to decide is written inline as [TO CONFIRM: …] so it is visible on the page itself
 * and cannot be missed during review.
 */
function legal_page( $k, $crumb, $title, $lead, array $blocks, $updated ) {
	global $C, $F_MONO;

	$body = [];
	$i    = 0;

	foreach ( $blocks as $block ) {
		list( $type, $content ) = $block;
		$i++;

		if ( 'h' === $type ) {
			/* signature is ( key, text, tag, desktop, mobile, align, link, extra ) —
			   the extra carries the spacing above each heading */
			$body[] = heading_item( $k . '-h' . $i, $content, 'h2', '26px', '22px', 'left', '',
				col_margin( '14px', '38px' ) );
			continue;
		}

		if ( 'ul' === $type ) {
			$html = '<ul style="margin:0 0 0 20px;padding:0">';

			foreach ( $content as $li ) {
				$html .= '<li style="margin:0 0 10px">' . $li . '</li>';
			}

			$html .= '</ul>';
			$body[] = text_item( $k . '-l' . $i, $html, [ 'size' => '17px', 'lh' => '1.75', 'title' => 'List' ] );
			continue;
		}

		$body[] = text_item( $k . '-p' . $i, $content, [ 'size' => '17px', 'lh' => '1.75', 'title' => 'Paragraph' ] );
	}

	$body[] = text_item( $k . '-updated',
		'<span style="font-family:\'JetBrains Mono\',monospace;font-size:12.5px;letter-spacing:0.05em;text-transform:uppercase;color:#7d8ea3">Last updated ' . $updated . '</span>',
		[ 'title' => 'Last updated', 'extra' => col_margin( '0px', '18px' ) ]
	);

	return [
		page_hero( $k . '-hero', [ [ 'Home', '/' ], [ $crumb, null ] ], $title, $lead ),

		section( $k . '-body', 'Legal text', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], [
			wrap( $k . '-body-w', 'Legal body', '1/1', [
				'css_advanced_flex'   => css( WRP, 'width', [ 'desktop' => '820px', 'tablet' => '100%', 'mobile' => '100%' ] ),
				'css_advanced_margin' => css( WRPI, 'margin', [ 'desktop' => dim( '0px', 'auto', '0px', 'auto' ) ] ),
			], $body ),
		] ),

		cta_band( $k . '-cta', 'Questions', 'Something here unclear?',
			'Write to us and we will point you to the right person.',
			[ [ 'Contact us', '/contact/', 'primary' ], [ 'Back to home', '/', 'ghost' ] ]
		),
	];
}

$pages[] = [
	'slug'  => 'technology',
	'title' => 'Technology',
	'desc'  => 'Every Miraex product is built on one proprietary foundation: thin-film lithium tantalate photonic integrated circuits engineered for high-efficiency electro-optic transduction.',
	'sections' => [
		page_hero( 'tech-hero',
			[ [ 'Home', '/' ], [ 'Technology', null ] ],
			'The TFLT photonic integrated circuit platform',
			'Every Miraex product is built on one proprietary foundation: thin-film lithium tantalate photonic integrated circuits engineered for high-efficiency electro-optic transduction.',
			[
				[ 'Request the platform brief', '/contact/', 'primary' ],
				[ 'See the verticals', '#verticals', 'ghost' ],
			]
		),

		/* why TFLT — image left, copy right */
		section( 'tech-why', 'Why TFLT', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
			'css_advanced_align_items'      => css( SECW, 'align-items', [ 'desktop' => 'center' ] ),
		], [
			wrap( 'tech-why-img', 'Image', '1/2', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '40px', '0px', '0px' ) ] ),
			], [
				framed_image( 'tech-why-image', $IMG['chip']['url'], 'Macro render of a thin-film lithium tantalate photonic integrated circuit with gold waveguides' ),
			] ),
			wrap( 'tech-why-copy', 'Copy', '1/2', [], array_merge(
				head_items( 'tech-why', 'Why TFLT', 'Thin-film lithium tantalate, engineered for quantum',
					'TFLT delivers the strong electro-optic response needed to move quantum information between microwave and optical light — with the low loss, low power and cryogenic compatibility that superconducting and spin-qubit systems demand. The platform is compact and power-efficient by design.',
					'left', '40px' ),
				tick_items( 'tech-why', [
					'<strong>Nonlinear parametric &amp; electro-optic processes</strong> on one platform — including optical–optical conversion and amplification',
					'<strong>Triply-resonant and traveling-wave device designs</strong>, covered by a patent portfolio',
					'<strong>Operates from Hz level</strong> to 100 GHz+, across a broad wavelength range from UV to 2 µm',
					'<strong>Customisable &amp; exclusive</strong> OEM designs and licensing schemes',
				] )
			) ),
		] ),

		/* spec + what it enables */
		section( 'tech-spec', 'Specifications & modes', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
		], [
			wrap( 'tech-spec-left', 'Spec', '1/2', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '40px', '0px', '0px' ) ] ),
			], [
				eyebrow( 'tech-spec-eyebrow', 'Specifications' ),
				heading_item( 'tech-spec-h2', 'Platform at a glance', 'h2', '48px', '32px' ),
				text_item( 'tech-spec-note', 'Representative platform capabilities. Detailed datasheets are available on request and under NDA.', [
					'color' => $C['slate'], 'size' => '15px', 'title' => 'Note',
				] ),
				spec_table( 'tech-spec-table', [
					'Material platform'    => 'Thin-Film Lithium Tantalate (TFLT)',
					'Function'             => 'Electro-optic transduction · optical modulation · optical–optical conversion &amp; parametric processes (e.g. amplification)',
					'Conversion bandwidth' => 'kHz → 100 GHz+',
					'Operating regime'     => 'Cryogenic / millikelvin compatible',
					'Architecture'         => 'TFLT photonic integrated circuit (patented)',
					'Conversion'           => 'Microwave↔optical · NIR↔optical',
					'Wavelength range'     => 'UV to IR (∼UV–2 µm)',
					'Availability'         => 'OEM designs · exclusive licensing',
				] ),
			] ),
			wrap( 'tech-modes', 'Modes', '1/2', [], array_merge( [
				eyebrow( 'tech-modes-eyebrow', 'Four modes, one platform' ),
				heading_item( 'tech-modes-h2', 'What the platform enables', 'h2', '48px', '32px' ),
			], app_items( 'tech-modes-app', [
				[ 'A', 'Transduction &amp; conversion', 'Coherent conversion between optical and microwave domains, plus optical–optical parametric processes such as amplification.' ],
				[ 'B', 'Entanglement generation &amp; distribution', 'Photon-pair entanglement across the microwave, NIR and optical domains.' ],
				[ 'C', 'RF over fibre', 'Highly efficient microwave-optical modulation from kHz to beyond 100 GHz.' ],
				[ 'D', 'Optical modulation', 'Electro-optic modulation across a broad wavelength range — from the UV and visible, through the NIR, up to the C-band.' ],
			] ) ) ),
		] ),

		/* verticals */
		section( 'tech-verticals', 'Verticals', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
			'section_id'                    => 'verticals',
		], array_merge( [
			wrap( 'tech-verticals-head', 'Section head', '1/1', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '19%', '48px', '19%' ), 'mobile' => dim( '0px', '0px', '32px', '0px' ) ] ),
			], head_items( 'tech-verticals', 'One platform, three verticals', 'From a single chip to a quantum infrastructure', '', 'center' ) ),
		], feature_cards( 'tech-verticals', [
			[ 'icon' => 'fas fa-microchip',        'title' => 'Computing',  'desc' => 'Interconnect distant QPUs into collective compute.',          'link' => '/distributed-quantum-computing/', 'more' => 'Explore' ],
			[ 'icon' => 'fas fa-satellite-dish',   'title' => 'Sensing',    'desc' => 'Entanglement-based distributed sensing &amp; RF photonics.',  'link' => '/quantum-sensing/',               'more' => 'Explore' ],
			[ 'icon' => 'fas fa-project-diagram',  'title' => 'Networking', 'desc' => 'Repeaters &amp; QKD toward the quantum internet.',           'link' => '/quantum-networking/',            'more' => 'Explore' ],
		] ) ) ),

		cta_band( 'tech-cta', 'Let’s connect', 'Building with photonic quantum interconnects?',
			'Whether you need an OEM transducer design, an exclusive licence, or an evaluation under NDA — let’s start a technical conversation.',
			[ [ 'Request the platform brief', '/contact/', 'primary' ], [ 'Browse resources', '/resources/', 'ghost' ] ]
		),
	],
];

/* ================================== 2-4. SOLUTION PAGES (shared shape) == */

/**
 * The three vertical pages share one skeleton: hero → copy+image split →
 * spec+applications split → [optional roadmap] → SEALSQ stack → CTA.
 */
function solution_page( $k, array $p ) {
	global $C;

	$sections = [
		page_hero( $k . '-hero',
			[ [ 'Home', '/' ], [ 'Solutions', '/technology/' ], [ $p['crumb'], null ] ],
			$p['h1'], $p['lead'], [ [ $p['cta_label'], '/contact/', 'primary' ] ]
		),
	];

	/* copy + image */
	$copy = wrap( $k . '-intro-copy', 'Copy', '1/2', [
		'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => $p['image_right'] ? dim( '0px', '40px', '0px', '0px' ) : dim( '0px', '0px', '0px', '40px' ) ] ),
	], array_merge(
		head_items( $k . '-intro', $p['intro_eyebrow'], $p['intro_h2'], $p['intro_lead'], 'left', '40px' ),
		tick_items( $k . '-intro', $p['ticks'] )
	) );

	$img = wrap( $k . '-intro-img', 'Image', '1/2', [], [
		framed_image( $k . '-intro-image', $p['image'], $p['image_alt'] ),
	] );

	$sections[] = section( $k . '-intro', 'Introduction', [
		'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
		'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		'css_advanced_align_items'      => css( SECW, 'align-items', [ 'desktop' => 'center' ] ),
	], $p['image_right'] ? [ $copy, $img ] : [ $img, $copy ] );

	/* spec + applications */
	$sections[] = section( $k . '-spec', 'Specifications & applications', [
		'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
		'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
	], array_merge( [
		wrap( $k . '-spec-left', 'Spec', '1/2', [
			'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '40px', '0px', '0px' ) ] ),
		], [
			eyebrow( $k . '-spec-eyebrow', 'Specifications' ),
			heading_item( $k . '-spec-h2', $p['spec_h2'], 'h2', '40px', '28px' ),
			text_item( $k . '-spec-note', 'Representative. Full datasheets on request under NDA.', [
				'color' => $C['slate'], 'size' => '15px', 'title' => 'Note',
			] ),
			spec_table( $k . '-spec-table', $p['spec'] ),
		] ),
		wrap( $k . '-apps', 'Applications', '1/2', [], array_merge( [
			eyebrow( $k . '-apps-eyebrow', 'Applications' ),
			heading_item( $k . '-apps-h2', $p['apps_h2'], 'h2', '40px', '28px' ),
		], app_items( $k . '-apps-app', $p['apps'] ) ) ),
	] ) );

	/* optional roadmap */
	if ( ! empty( $p['roadmap'] ) ) {
		$sections[] = section( $k . '-roadmap', 'Roadmap', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], array_merge( [
			wrap( $k . '-roadmap-head', 'Section head', '1/1', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '38%', '44px', '' ), 'mobile' => dim( '0px', '0px', '32px', '0px' ) ] ),
			], head_items( $k . '-roadmap', 'On the roadmap', 'Where we’re taking the interconnect',
				'Directions we are actively developing toward — beyond today’s superconducting focus.' ) ),
		], app_card_wraps( $k . '-roadmap-app', $p['roadmap'], '1/2' ) ) );
	}

	/* SEALSQ stack */
	$sections[] = section( $k . '-stack', 'Where this fits in the SEALSQ stack', [
		'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
		'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
	], array_merge( [
		wrap( $k . '-stack-head', 'Section head', '1/1', [
			'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '38%', '44px', '' ), 'mobile' => dim( '0px', '0px', '32px', '0px' ) ] ),
		], head_items( $k . '-stack', 'Better together', 'Where this fits in the SEALSQ stack',
			'Miraex’s interconnect layer compounds in value alongside the rest of the Quantum Sovereign Vertical Stack.' ) ),
	], feature_cards( $k . '-stack', $p['stack'] ) ) );

	$sections[] = cta_band( $k . '-cta', 'Let’s connect', $p['cta_h2'], $p['cta_lead'],
		[ [ $p['cta_label'], '/contact/', 'primary' ], [ 'Browse resources', '/resources/', 'ghost' ] ]
	);

	return $sections;
}

$pages[] = [
	'slug'  => 'distributed-quantum-computing',
	'title' => 'Distributed Quantum Computing',
	'desc'  => 'Miraex quantum converters bridge microwave processors to the optical domain so QPUs can be linked into a cluster over standard telecom fibre.',
	'sections' => solution_page( 'dqc', [
		'crumb'     => 'Distributed Quantum Computing',
		'h1'        => 'Quantum interconnects that scale computing beyond a single cryostat',
		'lead'      => 'Microwave quantum processors break records — but they are hard to scale within one fridge. Miraex quantum converters bridge to the optical domain so processors can be linked into a cluster over standard telecom fibre.',
		'cta_label' => 'Talk to engineering',
		'intro_eyebrow' => 'The missing link',
		'intro_h2'   => 'Bridge stationary and flying qubits',
		'intro_lead' => 'The energies of stationary qubits (microwave photons) and flying qubits (optical photons) differ by five orders of magnitude. Miraex quantum converters bridge that gap — transferring quantum information over long distances between microwave-frequency processing units using classical optical telecom fibre.',
		'ticks' => [
			'Increase the processing power of existing superconducting QPUs through a <strong>distributed architecture</strong>',
			'Connect distant quantum computers together over a <strong>quantum network</strong>',
			'A modular path to <strong>scale beyond the limits</strong> of a single device',
			'<strong>Compatible with spin-qubit</strong> processors as well as superconducting ones',
		],
		'image'       => $IMG['cryostat']['url'],
		'image_alt'   => 'A dilution refrigerator used to cool superconducting quantum processors',
		'image_right' => true,
		'spec_h2' => 'Miraex quantum converters',
		'spec' => [
			'Conversion'              => 'Microwave ↔ optical (bidirectional)',
			'Qubit bridge'            => 'Stationary (microwave) ↔ flying (optical)',
			'Qubit compatibility'     => 'Superconducting &amp; spin qubits',
			'Conversion efficiency'   => 'Target &gt;50%',
			'Added noise'             => 'Target &lt;10⁻⁴ added photons',
			'Transport medium'        => 'Classical optical telecom fibre',
			'Cryogenic compatibility' => 'Yes — superconducting-QPU compatible',
			'Architecture'            => 'TFLT photonic integrated circuit (patented)',
		],
		'apps_h2' => 'What it unlocks',
		'apps' => [
			[ '01', 'QPU clustering',   'Connect distant superconducting quantum computers over a quantum network.' ],
			[ '02', 'Modular scale-up', 'Boost computational resources without scaling a single device.' ],
			[ '03', 'Control &amp; read-out', 'Convert classical control and read-out signals between optical and microwave domains.' ],
		],
		'roadmap' => [
			[ '↗', 'Trapped-ion &amp; cold-atom scale-up', 'Extending optical interconnects toward architectures targeting 100–1,000s of qubits across these modalities.' ],
			[ '↗', 'Space &amp; dual-use applications',    'Exploring compact, radiation-tolerant links for satellite and defence deployments.' ],
		],
		'stack' => [
			[ 'icon' => 'fas fa-shield-alt',      'title' => 'Post-quantum silicon', 'desc' => 'SEALSQ QS7001 secures the classical control plane around your quantum links.', 'more' => 'SEALSQ', 'muted' => true ],
			[ 'icon' => 'fas fa-atom',            'title' => 'Quantum processors',   'desc' => 'EeroQ and ColibriTD provide the compute endpoints the interconnect links.',  'more' => 'EeroQ · ColibriTD', 'muted' => true ],
			[ 'icon' => 'fas fa-project-diagram', 'title' => 'Networking layer',     'desc' => 'Extend clustered compute across cities and continents with repeaters.',      'more' => 'Miraex', 'muted' => true ],
		],
		'cta_h2'   => 'Scaling a quantum processor?',
		'cta_lead' => 'Tell us about your architecture and we’ll show how an optical interconnect changes what’s possible.',
	] ),
];

$pages[] = [
	'slug'  => 'quantum-sensing',
	'title' => 'Quantum Sensing',
	'desc'  => 'Ultra-low-noise transducers and entanglement turned into a distributed sensing capability — for precision navigation, geophysics, defence and critical-infrastructure monitoring.',
	'sections' => solution_page( 'qsn', [
		'crumb'     => 'Quantum Sensing',
		'h1'        => 'Setting new standards with RF over fibre',
		'lead'      => 'Miraex’s quantum conversion platform turns ultra-low-noise transducers and entanglement into a distributed sensing capability — for precision navigation, geophysics, defence and critical-infrastructure monitoring.',
		'cta_label' => 'Discuss a sensing programme',
		'intro_eyebrow' => 'The conversion platform',
		'intro_h2'   => 'Beyond classical limits in remote sensing',
		'intro_lead' => 'Quantum properties of electromagnetic fields make it possible to break classical limits in many domains — especially remote sensing, where they sharply enhance sensitivity when detecting targets with very small radar cross-sections and weak returning echoes.',
		'ticks' => [
			'Highly efficient from a few MHz to <strong>over 100 GHz</strong> bandwidth',
			'<strong>High-sensitivity microwave detection</strong> down to ~hundreds of photons with our SEOMs — single-photon detection is handed off to a downstream qubit, SPAD or SNSPD',
			'Surpasses existing technology in <strong>sensitivity, EM immunity and compactness</strong>',
			'Miniaturised devices → <strong>higher spatial resolution</strong> and integrated form factors',
		],
		'image'       => $IMG['journey']['url'],
		'image_alt'   => 'Microwave-to-optical conversion visualised as waves entering a chip and leaving as light',
		'image_right' => true,
		'spec_h2' => 'Miraex Superconducting Electro-Optical Modulators (SEOMs)',
		'spec' => [
			'Type'                       => 'Direct microwave-photon → optical-photon transducer',
			'Sensitivity'                => '≈ hundreds of photons (SEOM); single-photon detection via downstream qubit / SPAD / SNSPD',
			'Bandwidth'                  => 'few MHz → 100 GHz+',
			'Compatibility'              => 'Cryogenic &amp; quantum-device compatible',
			'Entanglement — modalities'  => 'SC–SC, ion–SC',
			'Entanglement — encodings'   => 'Fock states, time-bin',
		],
		'apps_h2' => 'Where it’s used',
		'apps' => [
			[ '01', 'RF systems scale-up',              'Dense MIMO RF control &amp; read-out for superconducting quantum computers.' ],
			[ '02', 'Remote sensing',                   'Enhanced sensitivity for small radar cross-sections and weak echoes.' ],
			[ '03', 'Sovereign navigation &amp; defence', 'Precision navigation, geophysics and critical-infrastructure monitoring.' ],
		],
		'stack' => [
			[ 'icon' => 'fas fa-satellite',     'title' => 'Distributed orbital sensing', 'desc' => 'Quantum sensing payloads extend the QOSC to precision Earth observation.', 'more' => 'WISeSat · QOSC', 'muted' => true ],
			[ 'icon' => 'fas fa-shield-alt',    'title' => 'Trusted hardware',            'desc' => 'Post-quantum secure elements protect sensor data end to end.',            'more' => 'SEALSQ', 'muted' => true ],
			[ 'icon' => 'fas fa-network-wired', 'title' => 'Networked sensing',           'desc' => 'Distribute entanglement across sensor arrays over fibre or free space.',   'more' => 'Miraex', 'muted' => true ],
		],
		'cta_h2'   => 'Exploring quantum-enhanced sensing?',
		'cta_lead' => 'From defence to geophysics, tell us the signal you need to detect — we’ll tell you how entanglement helps.',
	] ),
];

$pages[] = [
	'slug'  => 'quantum-networking',
	'title' => 'Quantum Networking',
	'desc'  => 'Miraex quantum interconnects link multiple quantum modalities through high-efficiency frequency conversion — the connectivity layer beneath QKD and future quantum repeaters.',
	'sections' => solution_page( 'qnw', [
		'crumb'     => 'Quantum Networking',
		'h1'        => 'Connecting quantum resources across frequency domains',
		'lead'      => 'Miraex quantum interconnects link multiple quantum modalities through a high-efficiency frequency-conversion process — the connectivity layer that lets quantum resources be networked today, and that underpins full quantum repeaters as the technology matures.',
		'cta_label' => 'Plan a network',
		'intro_eyebrow' => 'Quantum interconnects',
		'intro_h2'   => 'Network connectivity, today and tomorrow',
		'intro_lead' => 'Miraex interconnects link quantum resources across frequency domains and over fibre — combining secure optical data links with entanglement-based connectivity. The same technology forms the basis for future quantum repeater stations that will extend quantum states beyond the ~100 km limit of traditional fibre, toward a global quantum internet.',
		'ticks' => [
			'A patented, <strong>scalable</strong> approach based on state-of-the-art PIC designs',
			'<strong>Supports all qubit modalities</strong> via a high-efficiency frequency-conversion process',
			'Secure <strong>optical data links</strong> — classical data over our RF-over-fibre platform — alongside <strong>quantum key distribution (QKD)</strong>',
			'Connects quantum resources to <strong>increase compute and sensing</strong> at scale',
		],
		'image'       => $IMG['orbital']['url'],
		'image_alt'   => 'A satellite delivering a quantum key distribution beam to Earth',
		'image_right' => true,
		'spec_h2' => 'Miraex quantum interconnect platform',
		'spec' => [
			'Function'             => 'Quantum interconnect · secure optical data links · repeater (roadmap)',
			'Modality support'     => 'Flexible — all qubit modalities',
			'Mechanism'            => 'High-efficiency frequency conversion',
			'Data links'           => 'RF-over-fibre (RFoF) for classical data, PQSC-ready',
			'Quantum secure links' => 'Quantum Key Distribution (QKD) &amp; entanglement distribution',
			'Architecture'         => 'TFLT PIC, patented &amp; scalable',
		],
		'apps_h2' => 'What it enables',
		'apps' => [
			[ '01', 'The quantum internet',       'Transmit quantum states around the world over an entangled network.' ],
			[ '02', 'Secure communication',       'Protect communications and transactions with QKD &amp; entanglement distribution.' ],
			[ '03', 'Secure optical data links',  'Classical data over RF-over-fibre alongside quantum channels on the same platform.' ],
		],
		'stack' => [
			[ 'icon' => 'fas fa-satellite',   'title' => 'Satellite QKD (QOSC)',       'desc' => 'Space-grade TFLT PICs provide the physical-layer substrate for satellite-based QKD.', 'more' => 'WISeSat · QOSC', 'muted' => true ],
			[ 'icon' => 'fas fa-lock',        'title' => 'Post-quantum cryptography',  'desc' => 'SEALSQ PQC secures the classical layer that orchestrates the quantum network.',      'more' => 'SEALSQ', 'muted' => true ],
			[ 'icon' => 'fas fa-globe',       'title' => 'Planetary scale',            'desc' => 'Repeaters chain links into a global, sovereign quantum backbone.',                    'more' => 'Miraex', 'muted' => true ],
		],
		'cta_h2'   => 'Designing a quantum network?',
		'cta_lead' => 'From metro QKD to satellite links, let’s map the repeaters and interconnects your architecture needs.',
	] ),
];

/* ========================================================= 5. ABOUT ==== */

$about_stats = [];
foreach ( [
	[ 'ab-s1', '10',    'Specialists in photonics &amp; quantum' ],
	[ 'ab-s2', '$6M+',  'Raised before SEALSQ acquisition' ],
	[ 'ab-s3', 'EPFL',  'Innovation Park, Ecublens' ],
	[ 'ab-s4', '2026',  'Acquired by SEALSQ' ],
] as $st ) {
	$about_stats[] = stat_item( $st[0], $st[1], $st[2] );
}

$pages[] = [
	'slug'  => 'about',
	'title' => 'Company',
	'desc'  => 'Founded at the EPFL Innovation Park, Miraex brings together deep expertise in RF-based quantum systems, nonlinear frequency mixing and photonic integrated circuit design — now part of SEALSQ.',
	'sections' => [
		page_hero( 'ab-hero',
			[ [ 'Home', '/' ], [ 'Company', null ] ],
			'A diverse team pioneering a sustainable future with photonic and quantum technology',
			'Founded at the EPFL Innovation Park on the shores of Lake Geneva, Miraex brings together deep expertise in RF-based quantum systems, nonlinear frequency mixing, and photonic integrated circuit design and manufacturing — now part of SEALSQ.',
			[ [ 'Work with us', '/contact/', 'primary' ], [ 'Open roles', '/careers/', 'ghost' ] ]
		),

		section( 'ab-stats', 'Stats', [
			'css_advanced_padding'          => section_pad( '80px', '80px', '64px', '64px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], [
			wrap( 'ab-stats-w', 'Stats', '1/1', [], $about_stats ),
		] ),

		section( 'ab-values', 'Values & milestones', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy900'] ),
		], array_merge( [
			wrap( 'ab-values-w', 'Values', '1/2', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '40px', '0px', '0px' ) ] ),
			], array_merge(
				head_items( 'ab-values', 'Our values', 'Shared innovation, openly pursued',
					'We believe shared innovation and transparency are powerful catalysts for progress, trust and sustainable growth — fostering a collaborative environment to accelerate the development of new technologies.', 'left', '40px' ),
				app_items( 'ab-values-app', [
					[ '<i class="fas fa-users"></i>', 'Team',           'A diverse, collaborative group spanning physics, photonics and engineering.' ],
					[ '<i class="fas fa-microchip"></i>', 'Technology', 'Patented TFLT photonic integrated circuits at the frontier of quantum.' ],
					[ '<i class="fas fa-leaf"></i>', 'Sustainability',  'Building enabling infrastructure for a responsible quantum future.' ],
				] )
			) ),
		], [
			wrap( 'ab-timeline-w', 'Milestones', '1/2', [], [
				eyebrow( 'ab-timeline-eyebrow', 'Milestones' ),
				heading_item( 'ab-timeline-h2', 'The road so far', 'h2', '48px', '32px' ),
				...timeline_items( 'ab-timeline', [
					[ 'date' => 'JUN 2023', 'title' => 'CHF 2.4M Innosuisse grant', 'content' => 'Non-dilutive support for quantum interconnects in distributed computing &amp; networking.' ],
					[ 'date' => 'OCT 2023', 'title' => 'Swiss–US Quantum Summit &amp; Quantum Industry Day', 'content' => 'Presented sensing solutions for SatCom and the long-term interconnect roadmap.' ],
					[ 'date' => 'DEC 2023', 'title' => 'Q2B Santa Clara', 'content' => 'Introduced to US-based venture panels.' ],
					[ 'date' => '2024',     'title' => 'Hello Tomorrow &amp; Q2B Tokyo', 'content' => 'Engaged EU and Japanese investor and partner networks.' ],
					[ 'date' => 'FEB 2025', 'title' => 'Venture Leaders Technology', 'content' => 'Selected for the Swiss National Startup Team’s Silicon Valley roadshow.' ],
					[ 'date' => 'JUN 2026', 'title' => 'Acquired by SEALSQ', 'content' => 'Miraex becomes the quantum interconnect layer of the Quantum Sovereign Vertical Stack.' ],
				] ),
			] ),
		] ) ),

		section( 'ab-logos', 'In good company', [
			'css_advanced_padding'          => section_pad( '128px', '128px', '80px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], [
			wrap( 'ab-logos-head', 'Section head', '1/1', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '', '44px', '' ) ] ),
			], head_items( 'ab-logos', 'Award-winning, strongly supported', 'In good company', '', 'center' ) ),
			wrap( 'ab-logos-w', 'Logos', '1/1', [], [ clients_strip( 'ab-clients' ) ] ),
		] ),

		cta_band( 'ab-cta', 'Now part of SEALSQ', 'One coherent quantum infrastructure',
			'As a SEALSQ company, Miraex’s TFLT platform forms the quantum interconnect backbone of the QOSC constellation and the Quantum Sovereign Vertical Stack — uniting post-quantum silicon, orbital infrastructure and distributed quantum sensing.',
			[ [ 'Explore SEALSQ', 'https://www.sealsq.com/', 'primary' ], [ 'Read the announcement', '/news/sealsq-acquires-miraex/', 'ghost' ] ],
			$C['navy900']
		),
	],
];

/* ========================================================== 6. NEWS ==== */

$pages[] = [
	'slug'  => 'news',
	'title' => 'News',
	'desc'  => 'Developments from the Miraex lab and the wider SEALSQ quantum group.',
	'sections' => [
		page_hero( 'nw-hero', [ [ 'Home', '/' ], [ 'News', null ] ],
			'News &amp; insights',
			'Developments from the Miraex lab and the wider SEALSQ quantum group.'
		),

		section( 'nw-list', 'News list', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], array_merge( [
			/* featured article */
			news_card( 'nw-feat', $IMG['orbital']['url'], 'Satellite quantum link',
				[ 'Featured', 'Group' ], '02 June 2026',
				'SEALSQ acquires 100% of Miraex, completing its Quantum Sovereign Vertical Stack',
				'The acquisition closes the quantum interconnect layer — uniting post-quantum silicon, orbital infrastructure and distributed quantum sensing under one sovereign, end-to-end architecture.',
				'/news/sealsq-acquires-miraex/', '1/1'
			),
		], [
			news_card( 'nw-c1', $IMG['cleanroom']['url'], 'Cleanroom', [ 'Research' ], '07 April 2026',
				'Q-Modus SNSF Bridge: cryogenic TFLT modulator chips',
				'Thin-film lithium tantalate modulators developed with EPFL’s Prof. Villanueva and Swiss partners.',
				'/news/q-modus-snsf-bridge/' ),
			news_card( 'nw-c2', $IMG['cryostat']['url'], 'Quantum hardware', [ 'Company' ], '26 February 2025',
				'Miraex joins the Swiss National Startup Team in Silicon Valley',
				'Selected from a record 200 applicants for the Venture Leaders Technology roadshow.',
				'/news/venture-leaders-technology/' ),
			news_card( 'nw-c3', $IMG['hero']['url'], 'Photonics', [ 'Group' ], '24 March 2026',
				'SEALSQ signs Letter of Intent to acquire Miraex',
				'The first step toward closing the quantum interconnect layer of the sovereign stack.',
				'/news/sealsq-acquires-miraex/' ),
		] ) ),

		cta_band( 'nw-cta', 'Let’s connect', 'Following the quantum interconnect story?',
			'Talk to us about the technology, or explore how it fits the wider SEALSQ quantum stack.',
			[ [ 'Get in touch', '/contact/', 'primary' ], [ 'Browse resources', '/resources/', 'ghost' ] ]
		),
	],
];

/* ------------------------------------------- news articles (child pages) */

/** Long-form article body: framed image, dateline, paragraphs, back link. */
function article_page( $k, array $a ) {
	global $C, $F_MONO, $F_TEXT;

	$body = [
		framed_image( $k . '-img', $a['image'], $a['title'] ),
		text_item( $k . '-dateline', $a['dateline'], [
			'color' => $C['cyan'], 'size' => '13px', 'weight' => '500', 'title' => 'Dateline',
		] ),
	];

	foreach ( $a['paragraphs'] as $i => $p ) {
		$body[] = text_item( $k . '-p' . $i, $p, [ 'size' => '17px', 'lh' => '1.75', 'title' => 'Paragraph ' . ( $i + 1 ) ] );
	}

	$body[] = button_item( $k . '-back', '← Back to news', '/news/', 'ghost', '1/2' );

	return [
		page_hero( $k . '-hero',
			[ [ 'Home', '/' ], [ 'News', '/news/' ], [ $a['category'], null ] ],
			$a['title'], $a['lead']
		),

		section( $k . '-body', 'Article', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], [
			wrap( $k . '-body-w', 'Article body', '1/1', [
				'css_advanced_flex'   => css( WRP, 'width', [ 'desktop' => '820px', 'tablet' => '100%', 'mobile' => '100%' ] ),
				'css_advanced_margin' => css( WRPI, 'margin', [ 'desktop' => dim( '0px', 'auto', '0px', 'auto' ) ] ),
			], $body ),
		] ),

		cta_band( $k . '-cta', 'Let’s connect', 'Want the detail behind the headline?',
			'We’re happy to brief teams, partners and press on the technology and the roadmap.',
			[ [ 'Contact us', '/contact/', 'primary' ], [ 'Browse resources', '/resources/', 'ghost' ] ]
		),
	];
}

$pages[] = [
	'slug'   => 'sealsq-acquires-miraex',
	'parent' => 'news',
	'title'  => 'SEALSQ acquires 100% of Miraex, completing its Quantum Sovereign Vertical Stack',
	'desc'   => 'SEALSQ Corp (NASDAQ: LAES) has acquired the entire share capital of Miraex SA — closing the quantum interconnect layer of its sovereign, end-to-end quantum architecture.',
	'sections' => article_page( 'na1', [
		'category' => 'Group',
		'title'    => 'SEALSQ acquires 100% of Miraex, completing its Quantum Sovereign Vertical Stack',
		'lead'     => 'SEALSQ Corp (NASDAQ: LAES), a subsidiary of WISeKey, has acquired the entire share capital of Miraex SA — closing the quantum interconnect layer of its sovereign, end-to-end quantum architecture.',
		'image'    => $IMG['orbital']['url'],
		'dateline' => '02 June 2026 · Geneva &amp; Ecublens, Switzerland',
		'paragraphs' => [
			'SEALSQ Corp (NASDAQ: LAES), a subsidiary of WISeKey International Holding Ltd (NASDAQ: WKEY; SIX: WIHN) and a global leader in post-quantum semiconductor and cybersecurity solutions, has completed the acquisition of 100% of Miraex SA, a developer of photonics-based quantum interconnect solutions headquartered at the EPFL Innovation Park in Ecublens, Switzerland.',
			'The investment was made through the SEALSQ Quantum Fund — an internal strategic initiative with $200 million of approved capital, of which over $65 million has been deployed. The acquisition closes the <strong>quantum interconnect layer</strong> of SEALSQ’s Quantum Sovereign Vertical Stack: the critical bridge between quantum computing hardware and quantum communication networks.',
			'Built around Miraex’s proprietary thin-film lithium tantalate (TFLT) photonic integrated circuit platform, the technology converts quantum information between microwave frequencies — the operating domain of superconducting and spin-based processors — and optical frequencies, the medium of choice for quantum communication. It is a foundational capability without which distributed quantum architectures cannot function at scale.',
			'Because TFLT PICs are compact, power-efficient and radiation-tolerant by design, the platform is uniquely aligned with space-based quantum infrastructure — reinforcing SEALSQ’s Quantum Orbital Space Cloud (QOSC) programme across Low Earth Orbit and beyond, from satellite-based QKD to distributed orbital sensing.',
			'“With Miraex now fully integrated, the Quantum Sovereign Vertical Stack is complete,” said Carlos Moreira, Chairman &amp; CEO of SEALSQ. Daniel Brau, CEO &amp; Co-Founder of Miraex, added that the TFLT platform was built “to be the connective tissue of the quantum ecosystem — linking quantum processors, sensors and networks into one coherent infrastructure.”',
		],
	] ),
];

$pages[] = [
	'slug'   => 'q-modus-snsf-bridge',
	'parent' => 'news',
	'title'  => 'Q-Modus SNSF Bridge: cryogenic TFLT modulator chips with EPFL',
	'desc'   => 'A research programme to develop thin-film lithium tantalate modulator chips for cryogenic operation, in close collaboration with EPFL and Swiss partners.',
	'sections' => article_page( 'na2', [
		'category' => 'Research',
		'title'    => 'Q-Modus SNSF Bridge: cryogenic TFLT modulator chips with EPFL',
		'lead'     => 'A research programme to develop thin-film lithium tantalate modulator chips for cryogenic operation, in close collaboration with EPFL and Swiss partners.',
		'image'    => $IMG['cleanroom']['url'],
		'dateline' => '07 April 2026',
		'paragraphs' => [
			'As part of the Q-Modus SNSF Bridge research programme, thin-film lithium tantalate modulator chips are being developed by Prof. Guillermo Villanueva, Associate Professor of the Advanced Nano-electromechanical Systems Laboratory at EPFL, in close collaboration with Miraex.',
			'The chips are designed to operate under cryogenic conditions and involve close collaboration with academic and industrial partners including Swiss PIC, PSI, FHNW and the Zurich-based startup ZuriQ — strengthening the Swiss photonics ecosystem around next-generation quantum interconnects.',
		],
	] ),
];

$pages[] = [
	'slug'   => 'venture-leaders-technology',
	'parent' => 'news',
	'title'  => 'Miraex joins the Swiss National Startup Team in Silicon Valley',
	'desc'   => 'Selected from a record 200 applicants, Miraex joined the Venture Leaders Technology roadshow to pitch in Silicon Valley.',
	'sections' => article_page( 'na3', [
		'category' => 'Company',
		'title'    => 'Miraex joins the Swiss National Startup Team in Silicon Valley',
		'lead'     => 'Selected from a record 200 applicants, Miraex joined the Venture Leaders Technology roadshow to pitch in Silicon Valley.',
		'image'    => $IMG['cryostat']['url'],
		'dateline' => '26 February 2025',
		'paragraphs' => [
			'A jury of professional investors and technology experts reviewed a record-breaking 200 applications and chose ten startups to pitch their companies in Silicon Valley as part of the Venture Leaders Technology programme.',
			'Miraex’s selection for the Swiss National Startup Team recognised the strength of its photonic and quantum technology and brought its quantum-interconnect vision to a global investor and partner audience.',
		],
	] ),
];

/* ===================================================== 7. RESOURCES ==== */

$pages[] = [
	'slug'  => 'resources',
	'title' => 'Resources',
	'desc'  => 'Datasheets, application notes and a glossary for engineers evaluating photonic quantum interconnects. Technical documents are available on request under NDA.',
	'sections' => [
		page_hero( 'rs-hero', [ [ 'Home', '/' ], [ 'Resources', null ] ],
			'Resources &amp; technical library',
			'Datasheets, application notes and a glossary for engineers evaluating photonic quantum interconnects. Technical documents are available on request under NDA.'
		),

		section( 'rs-library', 'Library', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], array_merge(
			feature_cards( 'rs-lib', [
				[ 'icon' => 'fas fa-file-alt',  'title' => 'TFLT platform brief', 'desc' => 'Overview of the thin-film lithium tantalate PIC platform.', 'link' => '/contact/', 'more' => 'Request' ],
				[ 'icon' => 'fas fa-microchip', 'title' => 'Quantum converter &amp; transducer datasheet', 'desc' => 'Microwave-to-optical converter and transducer specifications for distributed quantum computing and quantum sensing.', 'link' => '/contact/', 'more' => 'Request' ],
				[ 'icon' => 'fas fa-network-wired', 'title' => 'Quantum repeater brief', 'desc' => 'The repeater platform for quantum networking and QKD.', 'link' => '/contact/', 'more' => 'Request' ],
				[ 'icon' => 'fas fa-book',      'title' => 'Whitepaper: the sovereign quantum stack', 'desc' => 'How interconnects complete an end-to-end quantum architecture.', 'link' => '/contact/', 'more' => 'Request' ],
				[ 'icon' => 'fas fa-atom',      'title' => 'Glossary', 'desc' => 'Transduction, TFLT, QKD, entanglement distribution — the language of quantum interconnects.', 'link' => '#glossary', 'more' => 'Open' ],
			] ),
			[
				wrap( 'rs-glossary-head', 'Glossary head', '1/1', [
					'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '64px', '', '30px', '' ) ] ),
				], head_items( 'rs-glossary', 'Glossary', 'Key terms' ) ),
				wrap( 'rs-glossary-w', 'Glossary', '1/1', [], [
					item( 'rs-acc', 'accordion', 'Key terms', '1/1', array_merge( col_margin( '0px' ), [
						'title'      => '',
						'title_tag'  => 'h4',
						'open1st'    => 1,
						'icon'       => 'fas fa-plus',
						'icon_active'=> 'fas fa-minus',
						'tabs' => [
							[ 'title' => 'Microwave-to-optical transduction', 'content' => 'The coherent conversion of quantum information between microwave-frequency photons (used by superconducting and spin qubits) and optical photons (used for communication). Miraex performs this on a TFLT PIC.' ],
							[ 'title' => 'TFLT — thin-film lithium tantalate', 'content' => 'An electro-optic material platform offering the strong response, low loss and cryogenic compatibility needed for efficient quantum transduction in a compact, space-grade photonic integrated circuit.' ],
							[ 'title' => 'Quantum repeater', 'content' => 'A device that extends entanglement beyond the ~100 km limit of optical fibre, enabling quantum states to be transmitted over long distances toward a global quantum internet.' ],
							[ 'title' => 'QKD — quantum key distribution', 'content' => 'A method of sharing encryption keys whose security is guaranteed by physics, immune to quantum-computer attacks. Miraex’s transduction and entanglement provide a physical-layer substrate for satellite-based QKD.' ],
						],
						'css_questiontitle_color'         => css( ITEM . ' .accordion .question .title', 'color', $C['white'] ),
						'css_question_title_typography'   => css( ITEM . ' .accordion .question .title', 'typography', [
							'desktop'     => [ 'font-size' => '17px', 'font-family' => $F_DISPLAY ],
							'font-weight' => '600',
						] ),
						'css_question_answer_color'       => css( ITEM . ' .accordion .question .answer', 'color', $C['inkSoft'] ),
						'css_question_answer_typography'  => css( ITEM . ' .accordion .question .answer', 'typography', [
							'desktop' => [ 'font-size' => '15.5px', 'line-height' => '1.65', 'font-family' => $F_TEXT ],
						] ),
						'css_question_title_background'   => css( ITEM . ' .accordion .question .title', 'background', $C['surface'] ),
						'css_question_titlebefore_border_color' => css( ITEM . ' .accordion .question,' . ITEM . ' .accordion .question .title:before', 'border-color', $C['line'] ),
						'css_question_answer_background'  => css( ITEM . ' .accordion .question .answer', 'background', 'rgba(255,255,255,0.015)' ),
						'css_questiontitlei_color'        => css( ITEM . ' .accordion .question .title i', 'color', $C['cyan'] ),
						'css_question_title_transition'  => css( ITEM . ' .accordion .question .title', 'transition', '0.2s' ),
						'css_question_title_hover_bg'    => css( ITEM . ' .accordion .question .title:hover', 'background-color', $C['surface2'] ),
					] ) ),
				] ),
			]
		) ),

		cta_band( 'rs-cta', 'Let’s connect', 'Need a specification or an evaluation?',
			'Request datasheets and briefs, or ask about an evaluation under NDA — we’ll connect you with an engineer.',
			[ [ 'Request documents', '/contact/', 'primary' ], [ 'Talk to us', '/contact/', 'ghost' ] ]
		),
	],
];

/* ======================================================= 8. CAREERS ==== */

/** Role card: tag pills, title, copy, apply link. */
function role_card( $key, array $tags, $title, $desc ) {
	global $C;

	$tag_html = '';
	foreach ( $tags as $t ) {
		$tag_html .= '<span style="display:inline-block;font-family:\'JetBrains Mono\',monospace;font-size:11px;letter-spacing:0.06em;color:#19c6da;border:1px solid rgba(25,198,218,0.35);background:rgba(25,198,218,0.08);padding:4px 10px;border-radius:999px;margin-right:8px">' . $t . '</span>';
	}

	return wrap( $key . '-w', $title, '1/2', card_attr( '32px' ), [
		text_item( $key . '-tags', $tag_html, [ 'title' => 'Tags' ] ),
		heading_item( $key . '-h3', $title, 'h3', '21px', '19px' ),
		text_item( $key . '-desc', $desc, [ 'size' => '15px', 'lh' => '1.6', 'title' => 'Description' ] ),
		text_item( $key . '-apply', '<a href="/contact/">Apply &rarr;</a>', [
			'color' => $C['cyan'], 'size' => '14.5px', 'weight' => '600', 'title' => 'Apply',
		] ),
	], '1/2', '1/1' );
}

$pages[] = [
	'slug'  => 'careers',
	'title' => 'Careers',
	'desc'  => 'We’re an award-winning team at the EPFL Innovation Park building integrated circuits that bring quantum computers to practical scale.',
	'sections' => [
		page_hero( 'cr-hero', [ [ 'Home', '/' ], [ 'Careers', null ] ],
			'Build the connective tissue of the quantum era',
			'We’re an award-winning team at the EPFL Innovation Park, close to Lausanne on the shores of Lake Geneva — building integrated circuits that bring quantum computers to practical scale. Now with the reach of SEALSQ behind us.',
			[ [ 'Send an open application', '/contact/', 'primary' ] ]
		),

		section( 'cr-roles', 'Open roles', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
		], array_merge( [
			wrap( 'cr-roles-head', 'Section head', '1/1', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '', '44px', '' ) ] ),
			], head_items( 'cr-roles', 'Open roles', 'Join the team' ) ),
		], [
			role_card( 'cr-r1', [ 'Full-time', 'Ecublens' ], 'Photonic Test Engineer',
				'Develop and execute testing and characterisation strategies for our quantum hardware; build optical and electro-optical measurement setups for integrated photonic devices.' ),
			role_card( 'cr-r2', [ 'Internship', '6–12 mo' ], 'Micro-/Nanofabrication Engineer (Intern)',
				'Support cleanroom fabrication of TFLT photonic integrated circuits alongside our process team.' ),
			role_card( 'cr-r3', [ 'Internship', '4–12 mo' ], 'Photonic Testing Intern',
				'Hands-on optical characterisation and measurement of integrated photonic devices.' ),
			role_card( 'cr-r4', [ 'Internship', 'Cryo' ], 'Cryogenic Design &amp; Measurement (Intern)',
				'Contribute to cryogenic design and measurement for quantum transduction experiments.' ),
		], [
			wrap( 'cr-why', 'Why Miraex', '1/1', array_merge( card_attr( '32px' ), [
				'css_advanced_margin' => css( WRPI, 'margin', [ 'desktop' => dim( '40px', '', '0px', '' ) ] ),
			] ), [
				heading_item( 'cr-why-h3', 'Why Miraex', 'h3', '21px', '19px' ),
				text_item( 'cr-why-text', 'Industry-defining quantum hardware, a collaborative Swiss deep-tech culture, an incredible quality of life on Lake Geneva — and the scale, satellite infrastructure and post-quantum expertise of SEALSQ behind the mission.', [
					'size' => '15.5px', 'lh' => '1.6', 'title' => 'Why Miraex',
				] ),
			] ),
		] ) ),

		cta_band( 'cr-cta', 'Let’s connect', 'Don’t see your role?',
			'We’re always interested in exceptional photonics, quantum and engineering talent. Tell us how you’d contribute.',
			[ [ 'Send an open application', '/contact/', 'primary' ], [ 'Browse resources', '/resources/', 'ghost' ] ]
		),
	],
];

/* ======================================================= 9. CONTACT ==== */

$pages[] = [
	'slug'  => 'contact',
	'title' => 'Contact',
	'desc'  => 'Tell us about your processor, payload, sensor or network. We’ll route you to the right engineer.',
	'sections' => [
		page_hero( 'ct-hero', [ [ 'Home', '/' ], [ 'Contact', null ] ],
			'Let’s connect',
			'Tell us about your processor, payload, sensor or network. We’ll route you to the right engineer — and, where it fits, to the wider SEALSQ quantum stack.'
		),

		section( 'ct-body', 'Form & details', [
			'css_advanced_padding'          => section_pad( '110px', '128px', '72px', '80px' ),
			'css_advanced_background_color' => css( SEC, 'background-color', $C['navy800'] ),
			'css_advanced_align_items'      => css( SECW, 'align-items', [ 'desktop' => 'flex-start' ] ),
		], [
			wrap( 'ct-form', 'Form', '1/2', [
				'css_advanced_padding' => css( WRPI, 'padding', [ 'desktop' => dim( '0px', '40px', '0px', '0px' ) ] ),
			], [
				hs_form_item( 'ct-hsform' ),
			] ),
			wrap( 'ct-info', 'Details', '1/2', [], [
				card_item( 'ct-c1', 'fas fa-map-marker-alt', 'Miraex SA',
					'EPFL Innovation Park, Building L<br>Chemin de la Dent d’Oche 1B<br>1024 Ecublens, Switzerland' ),
				card_item( 'ct-c2', 'fas fa-envelope', 'Direct',
					'<a href="mailto:info@miraex.com">info@miraex.com</a><br><a href="tel:+41774062816">+41 77 406 28 16</a>' ),
				card_item( 'ct-c3', '',
					'<span style="display:inline-flex;align-items:center;gap:10px;font-size:13px;color:#aebdce;font-family:Inter,sans-serif;font-weight:400"><span style="font-family:\'JetBrains Mono\',monospace;font-size:11px;color:#04121b;background:#19c6da;padding:4px 10px;border-radius:999px">SEALSQ</span> Group</span>',
					'Part of SEALSQ (NASDAQ: LAES), a WISeKey company. For post-quantum semiconductors and the wider stack, visit <a href="https://www.sealsq.com/" target="_blank" rel="noopener">sealsq.com</a>.' ),
			] ),
		] ),
	],
];


/* ---- privacy policy ---- */

$pages[] = [
	'slug'  => 'privacy',
	'title' => 'Privacy Policy',
	'desc'  => 'How Miraex SA collects, uses and protects personal data on this website, who it is shared with, and what you can ask us to do with it.',
	'sections' => legal_page( 'pv', 'Privacy', 'Privacy Policy',
		'How Miraex SA collects, uses and protects personal data on this website — and what you can ask us to do with it.',
		[
			[ 'p', 'This policy covers <strong>miraex.com</strong>. It does not cover the products or services Miraex supplies under a separate agreement, or the websites of SEALSQ, WISeKey or any other company we link to.' ],

			[ 'h', 'Who is responsible' ],
			[ 'p', 'Miraex SA, EPFL Innovation Park, Building L, Chemin de la Dent d’Oche 1B, 1024 Ecublens, Switzerland, is the controller of the personal data described here. You can reach us at <a href="mailto:info@miraex.com">info@miraex.com</a>.' ],
			[ 'p', '<em>[TO CONFIRM: commercial register number (CHE-…), and whether a data protection officer, an EU representative (GDPR Art. 27) or a Swiss representative has been appointed. If one has, name them here.]</em>' ],
			[ 'p', 'Miraex is part of the SEALSQ group. <em>[TO CONFIRM: whether enquiry data is shared with SEALSQ Corp or WISeKey, for what purpose, and on what legal basis. If it is, that has to be stated below.]</em>' ],

			[ 'h', 'What this website collects' ],
			[ 'p', 'Two things, and only two.' ],
			[ 'ul', [
				'<strong>What you type into the contact form:</strong> first name, last name, work email, organisation, the nature of your enquiry, your message, and the fact that you ticked the consent box.',
				'<strong>Technical data your browser sends with every request</strong> — IP address, browser identification, the page requested and the time — which the hosting provider records in its server logs. <em>[TO CONFIRM: who hosts the site, in which country the logs are stored, and how long they are kept.]</em>',
			] ],
			[ 'p', 'As published, this website sets <strong>no cookies</strong>, runs no analytics, and loads no third-party scripts or fonts — nothing is stored on your device and no other company sees your visit. If that changes (for example if visitor analytics or marketing tracking is added), we will ask for your consent first and update this page before it goes live.' ],

			[ 'h', 'The contact form and HubSpot' ],
			[ 'p', 'When you submit the contact form, the data goes directly from your browser to <strong>HubSpot, Inc.</strong> (2 Canal Park, Cambridge, MA 02141, USA), the customer relationship system we use to handle enquiries. It is not stored on this website’s server or database. HubSpot processes it on our instructions, which means it is transferred to and stored in the United States.' ],
			[ 'p', '<em>[TO CONFIRM: that a data processing agreement with HubSpot is in place; whether the HubSpot EU data centre is used instead; and the transfer mechanism relied on — EU-US Data Privacy Framework certification and/or Standard Contractual Clauses. This paragraph should name it explicitly.]</em>' ],

			[ 'h', 'Why we are allowed to process it' ],
			[ 'ul', [
				'To answer your enquiry and take steps at your request before entering into a contract — GDPR Art. 6(1)(b).',
				'Our legitimate interest in operating, securing and maintaining the website — GDPR Art. 6(1)(f).',
				'Your consent, for anything beyond answering your enquiry, such as sending you material you did not ask for — GDPR Art. 6(1)(a). You can withdraw it at any time, and doing so does not affect what was done before.',
			] ],
			[ 'p', 'For visitors in Switzerland the corresponding grounds under the revised Federal Act on Data Protection (revFADP) apply.' ],

			[ 'h', 'How long we keep it' ],
			[ 'p', '<em>[TO CONFIRM: retention periods. Two are needed — one for enquiry records held in the CRM, one for server logs. A common shape is: enquiries kept for the duration of the commercial relationship plus a defined number of years; logs kept for a short, fixed period for security purposes.]</em>' ],

			[ 'h', 'Who else sees it' ],
			[ 'ul', [
				'HubSpot, as described above.',
				'Our hosting provider, which necessarily processes the technical data in its logs. <em>[TO CONFIRM: name them.]</em>',
				'Public authorities, where we are legally required to disclose.',
			] ],
			[ 'p', 'We do not sell personal data, and we do not share it for advertising.' ],

			[ 'h', 'Your rights' ],
			[ 'p', 'You can ask us for a copy of the personal data we hold about you, ask us to correct or delete it, ask us to restrict how we use it, ask for it in a portable format, object to processing based on legitimate interest, and withdraw any consent you gave. Write to <a href="mailto:info@miraex.com">info@miraex.com</a> and we will respond within the period the law allows. <em>[TO CONFIRM: whether a dedicated privacy address should be used instead.]</em>' ],
			[ 'p', 'If you are not satisfied with our answer, you can complain to the Swiss Federal Data Protection and Information Commissioner (FDPIC) or, in the EU or EEA, to the supervisory authority where you live or work.' ],

			[ 'h', 'Security' ],
			[ 'p', 'The site is served over an encrypted connection, and the contact form submits over an encrypted connection directly to HubSpot. Access to enquiry records is limited to the people at Miraex who need them to answer you.' ],

			[ 'h', 'Changes to this policy' ],
			[ 'p', 'If we change how we handle personal data, we will update this page and the date below before the change takes effect.' ],
		],
		'[TO CONFIRM: publication date]'
	),
];

/* ---- terms of service ---- */

$pages[] = [
	'slug'  => 'terms-of-service',
	'title' => 'Terms of Service',
	'desc'  => 'The terms on which Miraex SA makes this website available, including use of its content, the status of technical information, and how confidential material should be sent.',
	'sections' => legal_page( 'ts', 'Terms of Service', 'Terms of Service',
		'The terms on which Miraex SA makes this website available. They cover the website itself — not the supply of products, which is governed by a separate written agreement.',
		[
			[ 'p', 'By using <strong>miraex.com</strong> you accept these terms. If you do not accept them, please do not use the site.' ],

			[ 'h', 'Who operates this site' ],
			[ 'p', 'Miraex SA, EPFL Innovation Park, Building L, Chemin de la Dent d’Oche 1B, 1024 Ecublens, Switzerland — a SEALSQ company. <em>[TO CONFIRM: commercial register number (CHE-…) and VAT number, which Swiss law expects to appear here.]</em>' ],

			[ 'h', 'Using the site' ],
			[ 'p', 'You may read, download and print what is published here for your own information and for evaluating whether to work with us. You may not systematically extract or scrape the content, republish or resell it, present it as your own, or attempt to disrupt or gain unauthorised access to the site.' ],

			[ 'h', 'Intellectual property' ],
			[ 'p', 'The text, images, diagrams, layout and design of this site belong to Miraex or to its licensors. “Miraex”, the Miraex logo, and the SEALSQ and WISeKey marks are trademarks of their respective owners. Nothing published here grants you a licence under any patent, trademark, design or other intellectual property right. <em>[TO CONFIRM: preferred trademark and patent wording, and whether registration numbers should be listed.]</em>' ],

			[ 'h', 'Technical information is indicative' ],
			[ 'p', 'The specifications, performance figures, application notes and roadmap dates on this site describe technology that is under active development. They are indicative, may change without notice, and are not a warranty of performance, an offer, or a commitment to any delivery date. Binding specifications exist only in a signed agreement between you and Miraex.' ],

			[ 'h', 'Forward-looking statements' ],
			[ 'p', 'Miraex is part of SEALSQ Corp (NASDAQ: LAES), a WISeKey company. Statements on this site about future development, roadmaps, markets or capabilities are forward-looking and subject to risks and uncertainties; actual results may differ. Nothing here is investment advice or an invitation to invest, and no investment decision should be based on it. <em>[TO CONFIRM: whether SEALSQ investor relations require their standard safe-harbour wording to be reproduced verbatim.]</em>' ],

			[ 'h', 'Sending us information does not create an NDA' ],
			[ 'p', 'This matters for a deep-tech enquiry, so it is stated plainly: <strong>submitting the contact form or emailing us does not create any confidentiality obligation</strong>, even if your message says the enquiry is “under NDA”. Please do not send confidential, proprietary or export-controlled technical information until a written confidentiality agreement is in place. Tell us that is what you need and we will arrange one first.' ],

			[ 'h', 'Links to other sites' ],
			[ 'p', 'Where we link to a site we do not operate — SEALSQ, WISeKey, partners, press coverage — we are not responsible for its content or its handling of your data.' ],

			[ 'h', 'Liability' ],
			[ 'p', 'To the extent permitted by law, Miraex is not liable for loss arising from use of this site or reliance on its content. <em>[TO CONFIRM: this clause needs review by counsel — Swiss law does not permit liability to be excluded for unlawful intent or gross negligence, and mandatory consumer protections may apply.]</em>' ],

			[ 'h', 'Governing law' ],
			[ 'p', 'These terms are governed by Swiss law. <em>[TO CONFIRM: place of jurisdiction — normally the courts at the company’s registered seat — and any carve-out for mandatory consumer jurisdiction.]</em>' ],

			[ 'h', 'Changes' ],
			[ 'p', 'We may update these terms. The version published here, with the date below, is the one that applies.' ],
		],
		'[TO CONFIRM: publication date]'
	),
];

/* ==================================================== PERSISTENCE ====== */

$ids = [];

foreach ( $pages as $p ) {
	$existing = get_page_by_path( ( isset( $p['parent'] ) ? $p['parent'] . '/' : '' ) . $p['slug'] );

	$data = [
		'post_type'    => 'page',
		'post_title'   => wp_specialchars_decode( $p['title'] ),
		'post_name'    => $p['slug'],
		'post_status'  => 'publish',
		'post_content' => '',
		'post_author'  => 1,
		'post_parent'  => isset( $p['parent'] ) ? ( $ids[ $p['parent'] ] ?? 0 ) : 0,
	];

	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$id = wp_update_post( $data, true );
	} else {
		$id = wp_insert_post( $data, true );
	}

	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, "{$p['slug']}: " . $id->get_error_message() . "\n" );
		continue;
	}

	$ids[ $p['slug'] ] = $id;

	$nodes = mfn_store_template( $id, $p['sections'] );

	update_post_meta( $id, 'mfn-post-hide-title', '1' );
	update_post_meta( $id, 'mfn-post-remove-padding', '1' );
	update_post_meta( $id, '_miraex_meta_description', $p['desc'] );

	printf( "%-32s #%-4d sections=%-2d nodes=%-4d %s\n",
		$p['slug'], $id, count( $p['sections'] ), $nodes, get_permalink( $id ) );
}
