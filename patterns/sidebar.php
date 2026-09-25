<?php
/**
 * Title: Sidebar
 * Slug: creative-agency/sidebar
 * Keywords: sidebar
 * Description: Search, categories, recent posts, tags and a call to start a project.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"creative-agency-sidebar","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group creative-agency-sidebar"><!-- wp:group {"className":"is-style-creative-agency-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-creative-agency-panel has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search keyword","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-creative-agency-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-creative-agency-panel has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"className":"creative-agency-widget-title"} -->
<h2 class="wp-block-heading creative-agency-widget-title">Categories</h2>
<!-- /wp:heading -->

<!-- wp:categories {"showPostCounts":true} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-creative-agency-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-creative-agency-panel has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"className":"creative-agency-widget-title"} -->
<h2 class="wp-block-heading creative-agency-widget-title">Recent posts</h2>
<!-- /wp:heading -->

<!-- wp:latest-posts {"postsToShow":4,"displayPostDate":true,"displayFeaturedImage":true,"featuredImageAlign":"left","featuredImageSizeWidth":80,"featuredImageSizeHeight":80,"className":"creative-agency-recent"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-creative-agency-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-creative-agency-panel has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"className":"creative-agency-widget-title"} -->
<h2 class="wp-block-heading creative-agency-widget-title">Tags</h2>
<!-- /wp:heading -->

<!-- wp:tag-cloud {"className":"is-style-outline"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-creative-agency-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-creative-agency-panel has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"className":"creative-agency-widget-title"} -->
<h2 class="wp-block-heading creative-agency-widget-title">Start a project</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tell us what you are building and we will reply within two working days.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-creative-agency-underline"} -->
<div class="wp-block-button is-style-creative-agency-underline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Say hi</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
