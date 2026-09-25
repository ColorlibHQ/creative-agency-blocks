<?php
/**
 * Title: Posts with sidebar
 * Slug: creative-agency/hidden-posts-list
 * Description: The post list used by the blog and every archive, beside the sidebar.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|90"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--90)"><!-- wp:columns {"className":"creative-agency-with-sidebar","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns creative-agency-with-sidebar"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:query {"queryId":0,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"default"}} -->
<!-- wp:group {"className":"creative-agency-post-card","style":{"spacing":{"blockGap":"0"},"shadow":"var:preset|shadow|soft"},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-post-card" style="box-shadow:var(--wp--preset--shadow--soft)"><!-- wp:group {"className":"creative-agency-post-media","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-post-media"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"2/1"} /-->

<!-- wp:group {"className":"creative-agency-date-badge","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group creative-agency-date-badge"><!-- wp:post-date {"format":"j","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"creative-agency-date-badge__day"} /-->

<!-- wp:post-date {"format":"M","metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"creative-agency-date-badge__month"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"creative-agency-post-text","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group creative-agency-post-text"><!-- wp:post-title {"isLink":true,"className":"creative-agency-card-title"} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:group {"className":"creative-agency-post-meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group creative-agency-post-meta"><!-- wp:post-terms {"term":"category","className":"creative-agency-meta-terms"} /-->

<!-- wp:post-author-name {"className":"creative-agency-meta-author"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p>Nothing here yet. Try a search, or have a look at our work instead.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

<!-- wp:query-pagination {"paginationArrow":"arrow","className":"creative-agency-pagination","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:template-part {"slug":"sidebar","area":"uncategorized"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
