<?php
/**
 * Title: Header
 * Slug: creative-agency/header
 * Keywords: header, navigation
 * Block Types: core/template-part/header
 * Description: The site's initial in a blue tile, the navigation centred, and a Say hi link. It turns black and follows the page once you scroll.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-header is-layout-flex is-content-justification-space-between","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"nowrap","verticalAlignment":"center"}} -->
<div class="wp-block-group alignfull creative-agency-header is-layout-flex is-content-justification-space-between"><!-- wp:group {"className":"creative-agency-brand is-layout-flex","layout":{"type":"flex"}} -->
<div class="wp-block-group creative-agency-brand is-layout-flex"><!-- wp:site-logo {"width":100} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:navigation {"className":"creative-agency-nav","layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} /-->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right","flexWrap":"nowrap"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-creative-agency-underline"} -->
<div class="wp-block-button is-style-creative-agency-underline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Say hi</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"creative-agency-scheme-toggle"} -->
<div class="wp-block-button creative-agency-scheme-toggle"><a class="wp-block-button__link wp-element-button" href="#"><span class="screen-reader-text">Switch between light and dark mode</span></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
