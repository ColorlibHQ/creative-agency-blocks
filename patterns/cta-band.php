<?php
/**
 * Title: Call to action band
 * Slug: creative-agency/cta-band
 * Categories: creative-agency-sections
 * Keywords: cta, call to action, band
 * Description: A full-width band with one large link.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-band","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"backgroundColor":"primary","textColor":"on-primary","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-band has-on-primary-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"className":"creative-agency-band__link","style":{"typography":{"textAlign":"center"}},"fontSize":"heading"} -->
<p class="has-text-align-center creative-agency-band__link has-heading-font-size"><a href="<?php echo esc_url( home_url( '/work/' ) ); ?>">Visit our work</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
