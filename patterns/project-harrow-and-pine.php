<?php
/**
 * Title: Project: Harrow &amp; Pine
 * Slug: creative-agency/project-harrow-and-pine
 * Keywords: project, case study, portfolio
 * Description: A project page: large photograph, client, service and year, the story, and links to the previous and next projects.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-project","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"0"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-project" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:0"><!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/work-harrow.webp' ) ); ?>" alt="An amber glass dropper bottle with a blank white label, standing on a stone block" style="aspect-ratio:16/9;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:columns {"className":"creative-agency-project-meta","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns creative-agency-project-meta"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"creative-agency-meta-label","textColor":"secondary"} -->
<p class="creative-agency-meta-label has-secondary-color has-text-color">Client</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"contrast"} -->
<p class="has-contrast-color has-text-color">Harrow &amp; Pine</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"creative-agency-meta-label","textColor":"secondary"} -->
<p class="creative-agency-meta-label has-secondary-color has-text-color">Service</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"contrast"} -->
<p class="has-contrast-color has-text-color">Packaging and labels</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"creative-agency-meta-label","textColor":"secondary"} -->
<p class="creative-agency-meta-label has-secondary-color has-text-color">Year</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"contrast"} -->
<p class="has-contrast-color has-text-color">2024</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:social-links {"size":"has-normal-icon-size","className":"creative-agency-share","layout":{"type":"flex","justifyContent":"right"}} -->
<ul class="wp-block-social-links has-normal-icon-size creative-agency-share"><!-- wp:social-link {"url":"https://www.facebook.com/","service":"facebook"} /-->

<!-- wp:social-link {"url":"https://www.pinterest.com/","service":"pinterest"} /-->

<!-- wp:social-link {"url":"https://x.com/","service":"x"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:columns {"className":"creative-agency-project-details","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns creative-agency-project-details"><!-- wp:column {"width":"58.33%"} -->
<div class="wp-block-column" style="flex-basis:58.33%"><!-- wp:heading {"className":"creative-agency-project-title"} -->
<h2 class="wp-block-heading creative-agency-project-title">Project details</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Harrow &amp; Pine is a two-person apothecary in Mendocino that distils its own pine and cedar oils. Its first face oil needed to look as considered as the recipe, and to hold its own on a shelf crowded with louder bottles.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We kept the amber glass and the black dropper, drew a quiet label system from the founders' hand-written batch notes, chose a paper that survives a steamy bathroom, and designed the carton, the shipper and the refill pouch to match.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"wp-block-list is-style-creative-agency-dots"} -->
<ul class="wp-block-list is-style-creative-agency-dots"><!-- wp:list-item -->
<li>Label, carton and refill pouch, printed by a local press.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Artwork built for two bottle sizes and three seasonal oils.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A shop-counter display cut from the same board as the carton.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"41.66%"} -->
<div class="wp-block-column" style="flex-basis:41.66%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Live view</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"creative-agency-project-nav","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns creative-agency-project-nav"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"creative-agency-project-nav__label creative-agency-project-nav__label\u002d\u002dleft","style":{"typography":{"textAlign":"left"}}} -->
<p class="has-text-align-left creative-agency-project-nav__label creative-agency-project-nav__label--left">Previous</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"creative-agency-project-nav__title","style":{"typography":{"textAlign":"left"}}} -->
<h3 class="wp-block-heading has-text-align-left creative-agency-project-nav__title"><a href="<?php echo esc_url( home_url( '/work/whisk/' ) ); ?>">Whisk design system</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"creative-agency-project-nav__label creative-agency-project-nav__label\u002d\u002dright","style":{"typography":{"textAlign":"right"}}} -->
<p class="has-text-align-right creative-agency-project-nav__label creative-agency-project-nav__label--right">Next</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"creative-agency-project-nav__title","style":{"typography":{"textAlign":"right"}}} -->
<h3 class="wp-block-heading has-text-align-right creative-agency-project-nav__title"><a href="<?php echo esc_url( home_url( '/work/arcade-club/' ) ); ?>">Arcade Club app</a></h3>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
