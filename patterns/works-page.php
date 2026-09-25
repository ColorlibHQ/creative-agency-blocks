<?php
/**
 * Title: Works: project grid
 * Slug: creative-agency/works-page
 * Description: The project grid without its title, for the Work page.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-works","style":{"spacing":{"padding":{"top":"var:preset|spacing|90","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|70"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-works" style="padding-top:var(--wp--preset--spacing--90);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:paragraph {"className":"is-style-creative-agency-watermark"} -->
<p class="is-style-creative-agency-watermark">Projects</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"creative-agency-works__grid"} -->
<div class="wp-block-columns creative-agency-works__grid"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|70"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"creative-agency-work","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work"><!-- wp:group {"className":"creative-agency-work__media","layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work__media"><!-- wp:image {"aspectRatio":"46/47","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/work-nookdesk.webp' ) ); ?>" alt="A white desk with a laptop and a tablet set up in the bright corner of an office" style="aspect-ratio:46/47;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"creative-agency-work__more"} -->
<div class="wp-block-button creative-agency-work__more"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/nookdesk/' ) ); ?>">View details</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":3,"className":"is-style-creative-agency-marked creative-agency-work__title"} -->
<h3 class="wp-block-heading is-style-creative-agency-marked creative-agency-work__title"><a href="<?php echo esc_url( home_url( '/work/nookdesk/' ) ); ?>">Nookdesk booking app</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"creative-agency-work","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work"><!-- wp:group {"className":"creative-agency-work__media","layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work__media"><!-- wp:image {"aspectRatio":"46/47","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/work-whisk.webp' ) ); ?>" alt="A hand holding up a rainbow-coloured whisk dipped in meringue" style="aspect-ratio:46/47;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"creative-agency-work__more"} -->
<div class="wp-block-button creative-agency-work__more"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/whisk/' ) ); ?>">View details</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":3,"className":"is-style-creative-agency-marked creative-agency-work__title"} -->
<h3 class="wp-block-heading is-style-creative-agency-marked creative-agency-work__title"><a href="<?php echo esc_url( home_url( '/work/whisk/' ) ); ?>">Whisk design system</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|70"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:group {"className":"creative-agency-work","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work"><!-- wp:group {"className":"creative-agency-work__media","layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work__media"><!-- wp:image {"aspectRatio":"46/47","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/work-harrow.webp' ) ); ?>" alt="An amber glass dropper bottle with a blank white label, standing on a stone block" style="aspect-ratio:46/47;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"creative-agency-work__more"} -->
<div class="wp-block-button creative-agency-work__more"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/harrow-and-pine/' ) ); ?>">View details</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":3,"className":"is-style-creative-agency-marked creative-agency-work__title"} -->
<h3 class="wp-block-heading is-style-creative-agency-marked creative-agency-work__title"><a href="<?php echo esc_url( home_url( '/work/harrow-and-pine/' ) ); ?>">Harrow &amp; Pine packaging</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"creative-agency-work","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work"><!-- wp:group {"className":"creative-agency-work__media","layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-work__media"><!-- wp:image {"aspectRatio":"46/47","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/work-arcade-card.webp' ) ); ?>" alt="A phone with a neon lock screen lying on a white games console" style="aspect-ratio:46/47;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"creative-agency-work__more"} -->
<div class="wp-block-button creative-agency-work__more"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/arcade-club/' ) ); ?>">View details</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":3,"className":"is-style-creative-agency-marked creative-agency-work__title"} -->
<h3 class="wp-block-heading is-style-creative-agency-marked creative-agency-work__title"><a href="<?php echo esc_url( home_url( '/work/arcade-club/' ) ); ?>">Arcade Club app</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">More projects</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
