<?php
/**
 * Title: Story: photograph and text
 * Slug: creative-agency/story
 * Categories: creative-agency-sections
 * Keywords: about, story, image, text
 * Description: A photograph beside a heading, a paragraph and a link.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--90);padding-bottom:0"><!-- wp:columns {"verticalAlignment":"center","className":"creative-agency-split","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns are-vertically-aligned-center creative-agency-split"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/product-phone.webp' ) ); ?>" alt="A hand holding a phone with a travel app open, over a laptop keyboard"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"41.66%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:41.66%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading -->
<h2 class="wp-block-heading">We help you build your product and brand, big or small</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>From the first workshop to the app store listing, one team stays with the work. Start-ups get a studio that has shipped before; established brands get people who still care about the last pixel.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-creative-agency-underline"} -->
<div class="wp-block-button is-style-creative-agency-underline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Meet the studio</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
