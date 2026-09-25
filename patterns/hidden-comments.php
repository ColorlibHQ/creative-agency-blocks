<?php
/**
 * Title: Comments
 * Slug: creative-agency/hidden-comments
 * Description: The comments area for a single post.
 * Inserter: no
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:comments {"className":"creative-agency-comments"} -->
<div class="wp-block-comments creative-agency-comments"><!-- wp:comments-title /-->

<!-- wp:comment-template -->
<!-- wp:group {"className":"creative-agency-comment","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group creative-agency-comment"><!-- wp:avatar {"size":70,"style":{"border":{"radius":"50%"}}} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:comment-content /-->

<!-- wp:group {"className":"creative-agency-comment-meta","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
<div class="wp-block-group creative-agency-comment-meta"><!-- wp:comment-author-name /-->

<!-- wp:comment-date /-->

<!-- wp:comment-reply-link /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-numbers /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination -->

<!-- wp:post-comments-form /--></div>
<!-- /wp:comments -->
