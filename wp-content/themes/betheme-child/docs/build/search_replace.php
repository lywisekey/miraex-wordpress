<?php
/**
 * Change the site's domain safely, without WP-CLI.
 *
 * A plain SQL or sed replace corrupts this database. Page content is stored as
 * PHP-serialized arrays whose strings carry their own byte length:
 *
 *     s:66:"https://miraex.com/wp-content/uploads/2026/08/hero-photonics.jpg"
 *
 * Replacing the text without fixing the 66 makes unserialize() fail, and BeBuilder then
 * renders the page completely blank with nothing in any log. This unserializes each
 * value, walks it, replaces inside, and serializes it back, so the lengths stay right.
 *
 * Usage — dry run first, it changes nothing until you pass --apply:
 *
 *   php search_replace.php https://miraex.com https://new-domain.com
 *   php search_replace.php https://miraex.com https://new-domain.com --apply
 *
 * `guid` is left alone on purpose: WordPress uses it as a permanent identifier for feed
 * readers, and its own documentation says never to change it. It does not affect how
 * anything renders. Pass --include-guid if you disagree.
 */

require __DIR__ . '/bootstrap.php';

global $wpdb;

$args   = array_values( array_filter( array_slice( $argv, 1 ), function ( $a ) { return '-' !== $a[0]; } ) );
$apply  = in_array( '--apply', $argv, true );
$guid   = in_array( '--include-guid', $argv, true );

if ( count( $args ) < 2 ) {
	fwrite( STDERR, "Usage: php search_replace.php <old-url> <new-url> [--apply] [--include-guid]\n" );
	exit( 1 );
}

list( $from, $to ) = $args;

/** Replace inside strings at any depth, leaving everything else untouched. */
function miraex_walk( $value, $from, $to ) {
	if ( is_string( $value ) ) {
		return str_replace( $from, $to, $value );
	}

	if ( is_array( $value ) ) {
		foreach ( $value as $k => $v ) {
			$value[ $k ] = miraex_walk( $v, $from, $to );
		}

		return $value;
	}

	if ( is_object( $value ) ) {
		foreach ( get_object_vars( $value ) as $k => $v ) {
			$value->$k = miraex_walk( $v, $from, $to );
		}

		return $value;
	}

	return $value;
}

/** Tables and the columns worth rewriting, keyed by the column that identifies a row. */
$targets = [
	$wpdb->options  => [ 'key' => 'option_id',  'cols' => [ 'option_value' ] ],
	$wpdb->postmeta => [ 'key' => 'meta_id',    'cols' => [ 'meta_value' ] ],
	$wpdb->termmeta => [ 'key' => 'meta_id',    'cols' => [ 'meta_value' ] ],
	$wpdb->usermeta => [ 'key' => 'umeta_id',   'cols' => [ 'meta_value' ] ],
	$wpdb->comments => [ 'key' => 'comment_ID', 'cols' => [ 'comment_content', 'comment_author_url' ] ],
	$wpdb->posts    => [ 'key' => 'ID',         'cols' => array_merge(
		[ 'post_content', 'post_excerpt' ], $guid ? [ 'guid' ] : []
	) ],
];

printf( "%s\n  %s\n  -> %s\n\n", $apply ? 'REWRITING' : 'DRY RUN (nothing is written)', $from, $to );

$total = $serialized = 0;

foreach ( $targets as $table => $spec ) {
	foreach ( $spec['cols'] as $col ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `{$spec['key']}` AS id, `{$col}` AS val FROM `{$table}` WHERE `{$col}` LIKE %s",
				'%' . $wpdb->esc_like( $from ) . '%'
			)
		);

		if ( ! $rows ) {
			continue;
		}

		$changed = $ser = 0;

		foreach ( $rows as $row ) {
			$data = maybe_unserialize( $row->val );
			$was  = is_string( $data ) ? false : true;
			$new  = maybe_serialize( miraex_walk( $data, $from, $to ) );

			if ( $new === $row->val ) {
				continue;
			}

			/* Never write something that cannot be read back. */
			if ( $was && false === @unserialize( $new ) && 'b:0;' !== $new ) {
				fwrite( STDERR, "  refusing {$table}.{$col} #{$row->id}: result does not unserialize\n" );
				continue;
			}

			$changed++;
			$was && $ser++;

			if ( $apply ) {
				$wpdb->query(
					$wpdb->prepare( "UPDATE `{$table}` SET `{$col}` = %s WHERE `{$spec['key']}` = %d", $new, $row->id )
				);
			}
		}

		if ( $changed ) {
			printf( "  %-16s %-20s %4d rows%s\n", $table, $col, $changed, $ser ? " ({$ser} serialized)" : '' );
			$total      += $changed;
			$serialized += $ser;
		}
	}
}

printf( "\n%d rows%s, %d of them serialized.\n", $total, $apply ? ' rewritten' : ' would change', $serialized );

if ( ! $apply ) {
	echo "Re-run with --apply to write.\n";
	exit( 0 );
}

/* Derived data that has to be rebuilt rather than rewritten. */
foreach ( glob( wp_upload_dir()['basedir'] . '/rank-math/*.xml' ) as $file ) {
	unlink( $file );
}

delete_option( 'rank_math_sitemap_cache_files' );
flush_rewrite_rules( false );

echo "Sitemap cache cleared and rewrite rules flushed.\n";
echo "Still to do by hand: regenerate the BeTheme CSS, and register the new domain in HubSpot.\n";
