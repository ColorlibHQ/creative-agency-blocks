<?php
/**
 * Title: Post meta
 * Slug: creative-agency/hidden-post-meta
 * Description: Date and author for a single post.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"className":"creative-agency-post-meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group creative-agency-post-meta"><!-- wp:post-date {"metadata":{"bindings":{"datetime":{"source":"core/post-data","args":{"field":"date"}}}},"className":"creative-agency-meta-date"} /-->

<!-- wp:post-author-name {"className":"creative-agency-meta-author"} /--></div>
<!-- /wp:group -->
