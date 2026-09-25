<?php
/**
 * Title: Studio film
 * Slug: creative-agency/video
 * Categories: creative-agency-sections
 * Keywords: video, film, photo
 * Description: A full-width photograph of the studio with a play button that opens a video.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_theme_file_uri( 'assets/images/studio-meeting.webp' ) ); ?>","dimRatio":0,"overlayColor":"dark","isUserOverlayColor":true,"align":"full","className":"creative-agency-film","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull creative-agency-film"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/studio-meeting.webp' ) ); ?>" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-dark-background-color has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"creative-agency-video creative-agency-play"} -->
<div class="wp-block-button creative-agency-video creative-agency-play"><a class="wp-block-button__link wp-element-button" href="https://www.youtube.com/watch?v=rrT6v5sOwJg"><span class="screen-reader-text">Play the video</span></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div></div>
<!-- /wp:cover -->
