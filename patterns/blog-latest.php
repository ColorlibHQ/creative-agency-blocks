<?php
/**
 * Title: Latest posts
 * Slug: creative-agency/blog-latest
 * Categories: creative-agency-sections
 * Keywords: blog, posts, news, journal
 * Description: The three most recent posts as cards with a date badge.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-journal","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|90"},"blockGap":"var:preset|spacing|70"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-journal" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--90)"><!-- wp:paragraph {"className":"is-style-creative-agency-watermark"} -->
<p class="is-style-creative-agency-watermark">Journal</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">From the journal</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"className":"creative-agency-post-card","style":{"spacing":{"blockGap":"0"},"shadow":"var:preset|shadow|soft"},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-post-card" style="box-shadow:var(--wp--preset--shadow--soft)"><!-- wp:group {"className":"creative-agency-post-media","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-post-media"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"2/1"} /-->

<!-- wp:group {"className":"creative-agency-date-badge","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-date-badge"><!-- wp:post-date {"format":"j","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"creative-agency-date-badge__day"} /-->

<!-- wp:post-date {"format":"M","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"creative-agency-date-badge__month"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"creative-agency-post-text","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group creative-agency-post-text"><!-- wp:post-title {"level":3,"isLink":true,"className":"creative-agency-card-title"} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:group {"className":"creative-agency-post-meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group creative-agency-post-meta"><!-- wp:post-terms {"term":"category","className":"creative-agency-meta-terms"} /-->

<!-- wp:post-author-name {"className":"creative-agency-meta-author"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->
