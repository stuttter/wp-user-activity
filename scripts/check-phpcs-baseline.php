<?php
/**
 * Check the committed PHPCS baseline without loading WordPress.
 *
 * @package WPUserActivity
 */

declare(strict_types=1);

// This standalone command-line tool cannot use the WordPress filesystem API.
// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
// phpcs:disable WordPress.WP.AlternativeFunctions

$root          = dirname( __DIR__ );
$baseline_path = $root . '/phpcs-baseline.json';
$report_path   = tempnam( sys_get_temp_dir(), 'wpua-phpcs-' );
$generate      = in_array( '--generate', $argv, true );

if ( false === $report_path ) {
	fwrite( STDERR, "Unable to create a temporary PHPCS report.\n" );
	exit( 2 );
}

$command = array(
	PHP_BINARY,
	$root . '/vendor/bin/phpcs',
	'--standard=' . $root . '/phpcs.xml.dist',
	'--report=json',
	'--report-file=' . $report_path,
	'-q',
);

$escaped = array_map( 'escapeshellarg', $command );
exec( implode( ' ', $escaped ), $process_output, $status );

$report = json_decode( (string) file_get_contents( $report_path ), true );
unlink( $report_path );

// PHPCS uses statuses 1 and 2 for reported violations, depending on whether
// any are auto-fixable. Higher statuses represent an execution failure.
if ( $status > 2 ) {
	fwrite( STDERR, "PHPCS failed with exit status {$status}.\n" );
	exit( 2 );
}

if ( ! is_array( $report ) || ! isset( $report['files'] ) ) {
	fwrite( STDERR, "PHPCS did not produce a valid JSON report.\n" );
	exit( 2 );
}

$counts = array();
foreach ( $report['files'] as $file_path => $file ) {
	$relative_path = str_replace( $root . DIRECTORY_SEPARATOR, '', $file_path );

	foreach ( $file['messages'] as $message ) {
		$key            = $relative_path . '|' . $message['source'];
		$counts[ $key ] = isset( $counts[ $key ] ) ? $counts[ $key ] + 1 : 1;
	}
}
ksort( $counts );

if ( $generate ) {
	$written = file_put_contents( $baseline_path, json_encode( $counts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
	if ( false === $written ) {
		fwrite( STDERR, "Unable to write phpcs-baseline.json.\n" );
		exit( 2 );
	}
	fwrite( STDOUT, 'Recorded ' . array_sum( $counts ) . " existing PHPCS violations.\n" );
	exit( 0 );
}

if ( ! is_file( $baseline_path ) ) {
	fwrite( STDERR, "Missing phpcs-baseline.json.\n" );
	exit( 2 );
}

$baseline = json_decode( (string) file_get_contents( $baseline_path ), true );
if ( ! is_array( $baseline ) ) {
	fwrite( STDERR, "Invalid phpcs-baseline.json.\n" );
	exit( 2 );
}

$increases = array();
foreach ( $counts as $key => $count ) {
	$allowed = isset( $baseline[ $key ] ) ? (int) $baseline[ $key ] : 0;
	if ( $count > $allowed ) {
		$increases[] = sprintf( '%s increased from %d to %d.', $key, $allowed, $count );
	}
}

if ( $increases ) {
	fwrite( STDERR, implode( "\n", $increases ) . "\n" );
	exit( 1 );
}

$remaining = array_sum( $counts );
$original  = array_sum( $baseline );
fwrite( STDOUT, sprintf( "PHPCS baseline did not increase (%d remaining, %d recorded).\n", $remaining, $original ) );
