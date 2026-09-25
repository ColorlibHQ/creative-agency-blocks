<?php
/**
 * Title: Footer
 * Slug: creative-agency/footer
 * Keywords: footer
 * Block Types: core/template-part/footer
 * Description: Three columns on black — social links, pages and the studio's address — with a copyright line beneath.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","className":"creative-agency-footer","style":{"spacing":{"padding":{"top":"0","bottom":"0"},"blockGap":"0"}},"backgroundColor":"dark","textColor":"on-dark","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull creative-agency-footer has-on-dark-color has-dark-background-color has-text-color has-background" style="padding-top:0;padding-bottom:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns {"className":"creative-agency-footer-columns","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns creative-agency-footer-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"creative-agency-footer-title","textColor":"overlay"} -->
<h2 class="wp-block-heading creative-agency-footer-title has-overlay-color has-text-color">Follow us</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"wp-block-list is-style-creative-agency-links"} -->
<ul class="wp-block-list is-style-creative-agency-links"><!-- wp:list-item -->
<li><a href="https://www.instagram.com/">Instagram</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://dribbble.com/">Dribbble</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://www.behance.net/">Behance</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://www.linkedin.com/">LinkedIn</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://www.youtube.com/">YouTube</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"creative-agency-footer-title","textColor":"overlay"} -->
<h2 class="wp-block-heading creative-agency-footer-title has-overlay-color has-text-color">Links</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"wp-block-list is-style-creative-agency-links"} -->
<ul class="wp-block-list is-style-creative-agency-links"><!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Services</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/work/' ) ); ?>">Work</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"creative-agency-footer-title","textColor":"overlay"} -->
<h2 class="wp-block-heading creative-agency-footer-title has-overlay-color has-text-color">Address</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"creative-agency-footer-address","textColor":"on-dark"} -->
<p class="creative-agency-footer-address has-on-dark-color has-text-color">410 Broadway, Oakland, CA 94607<br><a href="mailto:studio@yourdomain.com">studio@yourdomain.com</a><br><a href="tel:+15105550142">+1 (510) 555-0142</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"creative-agency-footer-legal","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group creative-agency-footer-legal" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"creative-agency-copyright","style":{"typography":{"textAlign":"center"}},"textColor":"on-dark","fontSize":"medium"} -->
<p class="has-text-align-center creative-agency-copyright has-on-dark-color has-text-color has-medium-font-size">Copyright © <?php echo esc_html( gmdate( 'Y' ) ); ?> All rights reserved · Theme by <a href="https://colorlib.com/" rel="nofollow">Colorlib</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
