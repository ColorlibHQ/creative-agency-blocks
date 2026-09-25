<?php
/**
 * Title: Opening statement
 * Slug: creative-agency/hero
 * Categories: creative-agency-sections
 * Keywords: hero, intro, statement
 * Description: The page's opening line in large type, with a link beneath it and two floating shapes.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-hero","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-hero" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"level":1,"className":"creative-agency-statement"} -->
<h1 class="wp-block-heading creative-agency-statement">We are a <span style="text-decoration: underline;">design and development</span> <br>studio based in California</h1>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-creative-agency-underline"} -->
<div class="wp-block-button is-style-creative-agency-underline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">Browse our work</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
