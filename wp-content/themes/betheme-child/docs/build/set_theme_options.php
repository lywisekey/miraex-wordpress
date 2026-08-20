<?php
/**
 * Site-wide chrome that the reference design assumes: a dark page background so
 * no white shows behind/around the dark sections.
 */
require __DIR__ . '/bootstrap.php';
$opts = get_option( 'betheme' );
if ( ! is_array( $opts ) ) { fwrite( STDERR, "theme options missing\n" ); exit( 1 ); }

$before = [ $opts['background-html'] ?? '', $opts['background-body'] ?? '' ];

$opts['background-html'] = '#070f1c';
$opts['background-body'] = '#070f1c';

update_option( 'betheme', $opts );

printf( "background-html/body: %s -> #070f1c\n", implode( ' / ', $before ) ?: '(default #FCFCFC)' );
