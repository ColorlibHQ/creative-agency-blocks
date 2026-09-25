<?php
/**
 * Title: Contact: map, form and details
 * Slug: creative-agency/contact
 * Categories: creative-agency-sections
 * Keywords: contact, form, map
 * Description: A map, then the contact form beside the studio's address, phone and email.
 *
 * @package Creative_Agency
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|90"},"blockGap":"var:preset|spacing|60"}},"layout":{"type":"constrained"},"anchor":"contact"} -->
<div class="wp-block-group alignfull" id="contact" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--90)"><!-- wp:html -->
<iframe class="creative-agency-map" title="Map showing where the studio is" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=-122.2830%2C37.7930%2C-122.2680%2C37.8020&amp;layer=mapnik&amp;marker=37.7975%2C-122.2755" style="width:100%;height:480px;border:0"></iframe>
<!-- /wp:html -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"creative-agency-contact-title"} -->
<h2 class="wp-block-heading creative-agency-contact-title">Get in touch</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[creative_agency_contact_form]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"className":"creative-agency-contact-details","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group creative-agency-contact-details"><!-- wp:paragraph {"className":"creative-agency-detail creative-agency-icon\u002d\u002dmap-pin"} -->
<p class="creative-agency-detail creative-agency-icon--map-pin"><strong>410 Broadway, Oakland</strong><br>California, CA 94607</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"creative-agency-detail creative-agency-icon\u002d\u002ddevice-mobile"} -->
<p class="creative-agency-detail creative-agency-icon--device-mobile"><strong>+1 (510) 555-0142</strong><br>Monday to Friday, 9am to 6pm</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"creative-agency-detail creative-agency-icon\u002d\u002dmail"} -->
<p class="creative-agency-detail creative-agency-icon--mail"><strong>studio@yourdomain.com</strong><br>Send us your brief any time</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
