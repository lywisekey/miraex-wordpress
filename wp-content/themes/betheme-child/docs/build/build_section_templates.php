<?php
/**
 * Build the reusable-section library: every distinct section shape used across the
 * 13 pages is saved as a `template` post with mfn_template_type = 'section', so it
 * can be dropped into any page from BeBuilder's Templates panel.
 *
 * Two ways to insert one (BeTheme decides at insert time, not here — see
 * class-mfn-builder-ajax.php::_template):
 *   - plain insert  → the content is copied and uids are regenerated: an independent
 *                     preset, safe from a later re-run of the page builders.
 *   - global insert → the page stores only mfn_global_section_id and the content is
 *                     read from this template on every render: edit once, changes
 *                     everywhere.
 *
 * The section data is taken from the pages that already render correctly, so a
 * template can never drift from the live design. uids are re-derived from the
 * template key, so re-running this script is idempotent and never collides with
 * the uids on the source page.
 *
 * Idempotent. Run after build_home.php / build_pages.php.
 */

require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib.php';

/* ------------------------------------------------------------- the library */

$library = [
	/* slug                         template name                          page                            section title */
	[ 'cta-band',                   'Section — CTA band',                  'technology',                    'CTA band' ],
	[ 'page-hero',                  'Section — Page hero + buttons',       'technology',                    'Page hero' ],
	[ 'page-hero-plain',            'Section — Page hero, title only',     'resources',                     'Page hero' ],
	[ 'home-hero',                  'Section — Full-screen hero',          'home',                          'Hero' ],
	[ 'icon-cards',                 'Section — Icon card grid',            'home',                          'Area of expertise' ],
	[ 'split-text-image',           'Section — Split: text, ticks, image', 'technology',                    'Why TFLT' ],
	[ 'split-image-text',           'Section — Split: image + text',       'quantum-sensing',               'Introduction' ],
	[ 'spec-table',                 'Section — Spec table',                'technology',                    'Specifications & modes' ],
	[ 'stats-row',                  'Section — Stats row',                 'about',                         'Stats' ],
	[ 'stats-clients',              'Section — Stats + logo strip',        'home',                          'Proof' ],
	[ 'logo-strip',                 'Section — Logo strip',                'about',                         'In good company' ],
	[ 'news-cards',                 'Section — News cards',                'home',                          'Latest news — cards' ],
	[ 'news-list',                  'Section — News list',                 'news',                          'News list' ],
	[ 'head-with-link',             'Section — Head with link',            'home',                          'Latest news' ],
	[ 'milestones',                 'Section — Values & milestones',       'about',                         'Values & milestones' ],
	[ 'roadmap',                    'Section — Roadmap',                   'distributed-quantum-computing', 'Roadmap' ],
	[ 'layer-stack',                'Section — Layer stack',               'home',                          'Root-to-Qubit stack' ],
	[ 'resource-library',           'Section — Resource library',          'resources',                     'Library' ],
	[ 'job-list',                   'Section — Job list',                  'careers',                       'Open roles' ],
	/* a child page needs its full path, not just the slug */
	[ 'article-body',               'Section — Article body',              'news/sealsq-acquires-miraex',   'Article' ],
	[ 'contact-form',               'Section — Contact form + details',    'contact',                       'Form & details' ],
];

/* ------------------------------------------------------------- helpers ---- */

/** Titles carry em dashes and curly quotes — compare on letters and digits only. */
function tpl_norm( $s ) {
	return strtolower( preg_replace( '/[^a-z0-9]+/i', '', (string) $s ) );
}

function tpl_find_section( $page_slug, $title ) {
	$page = get_page_by_path( $page_slug, OBJECT, 'page' );

	if ( ! $page ) {
		return [ null, "page '$page_slug' not found" ];
	}

	$items = get_post_meta( $page->ID, 'mfn-page-items', true );

	if ( ! is_array( $items ) ) {
		return [ null, "page '$page_slug' has no builder data" ];
	}

	$want = tpl_norm( $title );

	foreach ( $items as $section ) {
		$have = tpl_norm( $section['title'] ?? '' );

		if ( $have === $want || 0 === strpos( $have, $want ) ) {
			return [ $section, $page->ID ];
		}
	}

	return [ null, "section '$title' not found on '$page_slug'" ];
}

/**
 * Re-derive every uid from the template key.
 *
 * The css_* attributes keep the literal `mfnuidelement` placeholder — it is only
 * resolved when the stylesheet is generated — so renaming the uids is enough to
 * give the template its own CSS, with no collision with the source page.
 */
function tpl_reuid( array $section, $key ) {
	$section['uid'] = uid( $key . '-s' );

	foreach ( ( $section['wraps'] ?? [] ) as $w => $wrap ) {
		$section['wraps'][ $w ]['uid'] = uid( $key . '-w' . $w );

		foreach ( ( $wrap['items'] ?? [] ) as $i => $item ) {
			$section['wraps'][ $w ]['items'][ $i ]['uid'] = uid( $key . '-w' . $w . '-i' . $i );
		}
	}

	return $section;
}

/* ------------------------------------------------------------- build ------ */

$made = 0;
$failed = [];

foreach ( $library as list( $slug, $name, $page_slug, $title ) ) {

	list( $section, $info ) = tpl_find_section( $page_slug, $title );

	if ( ! $section ) {
		$failed[] = $info;
		continue;
	}

	$section = tpl_reuid( $section, 'tpl-' . $slug );
	$section['title'] = $name;

	$existing = get_posts([
		'post_type'   => 'template',
		'name'        => 'section-' . $slug,
		'numberposts' => 1,
		'post_status' => 'any',
	]);

	if ( $existing ) {
		$tmpl_id = $existing[0]->ID;
		wp_update_post([ 'ID' => $tmpl_id, 'post_title' => $name, 'post_status' => 'publish' ]);
	} else {
		$tmpl_id = wp_insert_post([
			'post_type'   => 'template',
			'post_title'  => $name,
			'post_name'   => 'section-' . $slug,
			'post_status' => 'publish',
			'post_author' => 1,
		]);
	}

	update_post_meta( $tmpl_id, 'mfn_template_type', 'section' );

	$nodes = mfn_store_template( $tmpl_id, [ $section ] );

	printf( "  #%-4d %-38s from /%s/  nodes=%d\n", $tmpl_id, $name, $page_slug, $nodes );
	$made++;
}

printf( "\n%d section templates built", $made );

if ( $failed ) {
	printf( ", %d skipped:\n  - %s", count( $failed ), implode( "\n  - ", $failed ) );
}

echo "\n";
