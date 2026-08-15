<?php
/**
 * Betheme Child Theme
 *
 * @package Betheme Child Theme
 */

/**
 * Load child theme textdomains.
 */
function mfn_load_child_theme_textdomain() {
	load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
	load_child_theme_textdomain( 'betheme', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'mfn_load_child_theme_textdomain' );

/**
 * Keep the existing child-theme stylesheet and RTL support.
 */
function mfnch_enqueue_styles() {
	if ( is_rtl() ) {
		wp_enqueue_style( 'mfn-rtl', get_template_directory_uri() . '/rtl.css' );
	}

	wp_dequeue_style( 'style' );
	wp_enqueue_style(
		'style',
		get_stylesheet_directory_uri() . '/style.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'mfnch_enqueue_styles', 101 );

/**
 * Miraex page detection.
 *
 * Default targets:
 * - the site's front page
 * - a page with slug "miraex-home"
 *
 * Add more conditions with the `miraex_should_enqueue` filter if needed.
 */
function miraex_should_enqueue() {
	$load = is_front_page() || is_page( 'miraex-home' );
	return (bool) apply_filters( 'miraex_should_enqueue', $load );
}

/**
 * Add a stable namespace class to the body.
 */
function miraex_body_class( $classes ) {
	if ( miraex_should_enqueue() ) {
		$classes[] = 'miraex-page';
	}
	return $classes;
}
add_filter( 'body_class', 'miraex_body_class' );

/**
 * Load Miraex's scoped CSS/JS only on the target page(s).
 */
function miraex_enqueue_assets() {
	if ( ! miraex_should_enqueue() ) {
		return;
	}

	$uri  = get_stylesheet_directory_uri();
	$path = get_stylesheet_directory();

	wp_enqueue_style(
		'miraex-fonts',
		'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'miraex-content',
		$uri . '/assets/miraex/css/miraex.css',
		array(),
		filemtime( $path . '/assets/miraex/css/miraex.css' )
	);

	wp_enqueue_script(
		'miraex-content',
		$uri . '/assets/miraex/js/miraex.js',
		array(),
		filemtime( $path . '/assets/miraex/js/miraex.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'miraex_enqueue_assets', 120 );

/**
 * [miraex_asset path="img/hero-photonics.jpg"]
 *
 * Returns a child-theme asset URL. Useful inside BeBuilder HTML/Shortcode
 * elements without hard-coding the domain.
 */
function miraex_asset_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'path' => '',
		),
		$atts,
		'miraex_asset'
	);

	$path = ltrim( (string) $atts['path'], '/' );
	$base = get_stylesheet_directory_uri() . '/assets/miraex/';

	return esc_url( $base . $path );
}
add_shortcode( 'miraex_asset', 'miraex_asset_shortcode' );

/**
 * Resolve a WordPress page URL when a matching page exists; otherwise fall
 * back to a clean site URL. This keeps the imported source portable.
 */
function miraex_page_url( $slug ) {
	$slug = trim( (string) $slug, '/' );

	if ( 'home' === $slug || 'index' === $slug || '' === $slug ) {
		return home_url( '/' );
	}

	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/' . $slug . '/' );
}

/**
 * Optional rapid-bootstrap shortcode.
 *
 * [miraex_homepage]
 *
 * This renders the homepage content only (no header/footer). Header and
 * footer remain controlled by BeTheme Header Builder / Footer Builder.
 * Use this only if you want to get the source content live quickly before
 * converting individual sections to native BeBuilder elements.
 */
function miraex_homepage_shortcode() {
	$file = get_stylesheet_directory() . '/partials/miraex-homepage.php';

	if ( ! file_exists( $file ) ) {
		return '';
	}

	ob_start();
	include $file;
	return ob_get_clean();
}
add_shortcode( 'miraex_homepage', 'miraex_homepage_shortcode' );

/**
 * Contact form endpoint: validates and rate-limits on the server, then forwards to
 * HubSpot so its identifiers stay out of the page. See inc/hubspot-proxy.php.
 */
require_once get_stylesheet_directory() . '/inc/hubspot-proxy.php';

/**
 * Removes the version numbers and discovery links WordPress prints by default.
 * See inc/hardening.php for what this does and does not achieve.
 */
require_once get_stylesheet_directory() . '/inc/hardening.php';
