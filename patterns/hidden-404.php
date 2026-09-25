<?php
/**
 * Title: 404 content
 * Slug: creative-agency/hidden-404
 * Description: What a visitor sees when nothing is there.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-hero" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"level":1,"className":"creative-agency-statement"} -->
<h1 class="wp-block-heading creative-agency-statement">That page has moved on</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The address may be old, or the page may have been renamed. Try a search, or start again from the home page.</p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search the site","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"creative-agency-search-404"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-creative-agency-underline"} -->
<div class="wp-block-button is-style-creative-agency-underline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to the home page</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
