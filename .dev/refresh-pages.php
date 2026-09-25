<?php
/**
 * Rebuild the starter pages on a throwaway Playground, the way activation does.
 *
 * Starter pages are COPIES of the patterns made at activation, so after a
 * pattern changes the running site still shows the old copy. This deletes the
 * pages and the menu the theme made and runs the theme's own activation
 * routine again, so what you look at is what a fresh install gets.
 *
 * Development only: .dev/ is not in the zip, and this refuses any host but
 * the local machine.
 *
 *   curl http://127.0.0.1:9492/wp-content/themes/creative-agency/.dev/refresh-pages.php
 *
 * @package Creative_Agency
 */

$creative_agency_host = isset( $_SERVER['HTTP_HOST'] ) ? preg_replace( '/:\d+$/', '', $_SERVER['HTTP_HOST'] ) : '';
if ( ! in_array( $creative_agency_host, array( '127.0.0.1', 'localhost' ), true ) ) {
	http_response_code( 403 );
	exit( 'local only' );
}

require dirname( __DIR__, 4 ) . '/wp-load.php';

foreach ( get_posts( array( 'post_type' => array( 'page', 'wp_navigation' ), 'post_status' => 'any', 'numberposts' => -1 ) ) as $creative_agency_post ) {
	wp_delete_post( $creative_agency_post->ID, true );
}
delete_option( CREATIVE_AGENCY_SETUP_FLAG );
update_option( 'show_on_front', 'posts' );

// Posts made by the blueprint before anyone was logged in have no author.
foreach ( get_posts( array( 'post_type' => 'post', 'numberposts' => -1, 'author' => 0 ) ) as $creative_agency_post ) {
	if ( ! $creative_agency_post->post_author ) {
		wp_update_post( array( 'ID' => $creative_agency_post->ID, 'post_author' => 1 ) );
	}
}

// As a site administrator, the way activation runs. The registry is populated
// on init, which wp-load has run.
wp_set_current_user( 1 );
creative_agency_create_front_page();

header( 'Content-Type: text/plain' );
foreach ( get_posts( array( 'post_type' => 'page', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $creative_agency_post ) {
	echo esc_html( get_permalink( $creative_agency_post ) ) . "\n";
}
echo 'front page: ' . (int) get_option( 'page_on_front' ) . "\n";
