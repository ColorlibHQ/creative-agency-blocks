<?php
/**
 * Run the demo importer (.dev/demo/import.php) on a throwaway Playground.
 *
 * On the real demo the lead runs it with `wp eval "require '…/import.php';"`.
 * This does the same over HTTP for a Playground, from inside a function so the
 * importer sees the same (non-global) scope `wp eval` gives it. Safe to call
 * any number of times: the importer is idempotent.
 *
 * Development only: .dev/ is not in the zip, and this refuses any host but
 * the local machine.
 *
 *   curl http://127.0.0.1:9492/wp-content/themes/creative-agency/.dev/import-demo.php
 *
 * @package Creative_Agency
 */

$creative_agency_host = isset( $_SERVER['HTTP_HOST'] ) ? preg_replace( '/:\d+$/', '', $_SERVER['HTTP_HOST'] ) : '';
if ( ! in_array( $creative_agency_host, array( '127.0.0.1', 'localhost' ), true ) ) {
	http_response_code( 403 );
	exit( 'local only' );
}

require dirname( __DIR__, 4 ) . '/wp-load.php';

header( 'Content-Type: text/plain' );

( static function () {
	require __DIR__ . '/demo/import.php';
} )();
