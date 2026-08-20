<?php
/**
 * Hand the meta descriptions written during the build over to Rank Math, and set the
 * site-wide social defaults. Idempotent.
 *
 * The builders store their copy in `_miraex_meta_description`, which nothing renders —
 * this moves it into `rank_math_description` and leaves the original in place as the
 * source of truth (re-running a page builder rewrites it, then re-run this).
 */

require __DIR__ . '/bootstrap.php';

if ( ! defined( 'RANK_MATH_VERSION' ) && ! class_exists( 'RankMath' ) ) {
	fwrite( STDERR, "Rank Math is not active — run install_rankmath.php first.\n" );
	exit( 1 );
}

/* ---------------------------------------------- 1. per-page descriptions -- */

$rows = get_posts([
	'post_type'   => [ 'page', 'post' ],
	'numberposts' => -1,
	'post_status' => 'any',
	'meta_key'    => '_miraex_meta_description',
]);

$moved = 0;

foreach ( $rows as $post ) {
	$desc = trim( (string) get_post_meta( $post->ID, '_miraex_meta_description', true ) );

	if ( '' === $desc ) {
		continue;
	}

	if ( get_post_meta( $post->ID, 'rank_math_description', true ) === $desc ) {
		continue;
	}

	update_post_meta( $post->ID, 'rank_math_description', $desc );
	printf( "  #%-4d %-52s %d chars\n", $post->ID, mb_substr( $post->post_title, 0, 52 ), mb_strlen( $desc ) );
	$moved++;
}

/* ------------------------------------------------- 2. site identity ------- */

/* The Site Title had been set to the hero headline, so every <title>, the RSS feed
   and every WordPress email carried a 58-character sentence as the brand name. */
if ( 'Miraex' !== get_option( 'blogname' ) ) {
	printf( "site title: %s -> Miraex\n", get_option( 'blogname' ) );
	update_option( 'blogname', 'Miraex' );
}

if ( ! get_option( 'blogdescription' ) ) {
	update_option( 'blogdescription', 'Connecting Quantum' );
}

/* ------------------------------------------------- 3. the front page ------ */

$home_desc = 'Miraex builds thin-film lithium tantalate photonic integrated circuits — the '
	. 'quantum interconnect layer linking quantum processors, sensors and networks into one '
	. 'coherent infrastructure. Part of the SEALSQ quantum sovereign stack.';

$titles = get_option( 'rank_math_options_titles', [] );
$titles = is_array( $titles ) ? $titles : [];

$titles['homepage_title']       = '%sitename% — Connecting Quantum';
$titles['homepage_description'] = $home_desc;

/* social preview: the hero artwork, so a shared link is not a bare URL */
$og_id = 0;

foreach ( get_posts([ 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => 'any' ]) as $att ) {
	$src = (string) get_post_meta( $att->ID, '_miraex_src_url', true );

	if ( false !== strpos( $src, 'hero' ) ) {
		$og_id = $att->ID;
		break;
	}
}

if ( $og_id ) {
	$url = wp_get_attachment_url( $og_id );

	/* site-wide fallback, used by every page that has no image of its own */
	$titles['open_graph_image']    = $url;
	$titles['open_graph_image_id'] = $og_id;

	/* the front page is special: Image::set_front_page_image() reads the page's own
	   meta and then `homepage_facebook_image_id`, and returns — it never falls back
	   to the site-wide image, so it has to be set separately. */
	$titles['homepage_facebook_image']    = $url;
	$titles['homepage_facebook_image_id'] = $og_id;

	/* The site-wide fallback only covers some contexts (`pt_*_facebook_image` is for
	   archives, and singular pages resolve their image from post meta first), and no
	   page here has a featured image — so give every page the default explicitly. */
	foreach ( get_posts([ 'post_type' => [ 'page', 'post' ], 'numberposts' => -1, 'post_status' => 'publish' ]) as $p ) {
		if ( get_post_thumbnail_id( $p->ID ) || get_post_meta( $p->ID, 'rank_math_facebook_image_id', true ) ) {
			continue;
		}

		update_post_meta( $p->ID, 'rank_math_facebook_image', $url );
		update_post_meta( $p->ID, 'rank_math_facebook_image_id', $og_id );
	}
}

$og = $og_id;

/* With a static front page Rank Math reads the page's own fields, not the Homepage
   tab — both are set so the value survives either way. */
$front = (int) get_option( 'page_on_front' );

if ( $front ) {
	update_post_meta( $front, 'rank_math_title', 'Miraex — Connecting Quantum' );

	if ( $og_id ) {
		update_post_meta( $front, 'rank_math_facebook_image', wp_get_attachment_url( $og_id ) );
		update_post_meta( $front, 'rank_math_facebook_image_id', $og_id );
	}

	update_post_meta( $front, 'rank_math_description', $home_desc );
	update_post_meta( $front, '_miraex_meta_description', $home_desc );
}

update_option( 'rank_math_options_titles', $titles );

/* ------------------------------------------------- 4. quiet the wizard ---- */

update_option( 'rank_math_wizard_completed', true );
update_option( 'rank_math_registration_skip', true );

/* Rank Math registers its sitemap routes as rewrite rules and takes over
   /wp-sitemap.xml. Activating from the CLI does not flush them, which leaves the site
   with no sitemap at all: the core one redirects away and sitemap_index.xml 404s. */
flush_rewrite_rules( false );

/* Rank Math caches each sitemap as an XML file under uploads/rank-math/ and serves it
   verbatim ("Served from cache" in the footer of the output), so a page added after the
   last generation never appears. Its own invalidator goes through WP_Filesystem, which
   falls back to FTP on the CLI and fatals — delete the files directly instead. */
$cache_dir = wp_upload_dir()['basedir'] . '/rank-math';
$cleared   = 0;

foreach ( (array) glob( $cache_dir . '/*.xml' ) as $file ) {
	if ( unlink( $file ) ) {
		$cleared++;
	}
}

delete_option( 'rank_math_sitemap_cache_files' );

printf( "sitemap cache cleared (%d file%s)\n", $cleared, 1 === $cleared ? '' : 's' );

printf( "\n%d page descriptions moved into Rank Math\n", $moved );
printf( "front page: title + description set%s\n", $og_id ? ', OG image = ' . basename( $titles['open_graph_image'] ) : ' (no OG image found)' );
