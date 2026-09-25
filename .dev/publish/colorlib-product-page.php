<?php
/**
 * Build the Creative Agency product page on colorlib.com/wp, as a child of the
 * themes listing (5091).
 *
 * Same shape as Unioncorp's page (unioncorp-blocks/.dev/publish/), with three
 * differences the release brief asked for: no Documentation button (there is no
 * docs page yet), a "Two versions of Creative Agency" section straight after the
 * hero image (this block theme beside the older Elementor edition), and FAQs
 * rewritten for this theme.
 *
 * Every factual claim below was checked against this theme's code, not carried
 * over from Unioncorp's page:
 *  - contact form: inc/contact-form.php (server-side validation, nonce,
 *    honeypot, POST then redirect; `creative_agency_contact_handlers` hands a
 *    message to something else, `creative_agency_contact_email_to` changes the
 *    address). It is a plain POST form, so it works with JavaScript off.
 *  - update check: inc/updates.php sends theme, version, wp, php, locale,
 *    multisite and a site id that is an HMAC of home_url() keyed with the
 *    site's AUTH salt; `creative_agency_check_for_updates` turns it off.
 *  - form plugins: assets/css/forms.css styles six (Contact Form 7, WPForms,
 *    Gravity Forms, Fluent Forms, Forminator, Ninja Forms). inc/forms.php also
 *    detects Formidable and HappyForms but nothing styles them, so they are not
 *    claimed.
 *  - counts: 47 patterns = 17 sections + 9 pages in the inserter, 19 hidden
 *    (Inserter: no) + header and footer; 10 templates, 3 parts; 6 palettes
 *    (styles/colors), 5 type pairings (styles/typography); 10 starter pages.
 *  - motion (assets/js/interactions.js): header pins and turns black past
 *    400px, figures count up, the play button opens YouTube (nocookie) in a
 *    dialog; the studio photos use core's lightbox. Reduced motion stops the
 *    counting; `creative_agency_sticky_header` and
 *    `creative_agency_enable_scroll_animations` switch the other two off.
 *  - WooCommerce: inc/woocommerce.php loads woocommerce.css only when
 *    WooCommerce is active and only on shop, product, cart, checkout and
 *    account pages.
 *  - Elementor edition: github.com/ColorlibHQ/creative-agency (theme) needs
 *    Elementor and the Creative Agency Companion plugin
 *    (github.com/ColorlibHQ/creative-agency-companion), whose widgets build the
 *    sections; its demo is colorlibhub.com/creative-agency/.
 *
 * Idempotent: creates the page the first time, rewrites it after that, and
 * leaves it a DRAFT on creation. It never demotes a page already published.
 *
 * Images are found by file name, not by attachment ID, and the script refuses
 * to save while any is missing.
 *
 *   wp --url=https://colorlib.com/wp/ eval "require '/path/to/colorlib-product-page.php';"
 *
 * Use `wp eval "require …"`, not `wp eval-file`, which runs in a function scope.
 */

defined( 'ABSPATH' ) || exit;

$slug   = 'creative-agency';
$parent = 5091;
$title  = 'Creative Agency';

$download       = 'https://updates.colorlib.com/download/theme/creative-agency.zip';
$demo           = 'https://colorlibhub.com/creative-agency-blocks/';
$elementor_demo = 'https://colorlibhub.com/creative-agency/';
$elementor_repo = 'https://github.com/ColorlibHQ/creative-agency';
$companion_repo = 'https://github.com/ColorlibHQ/creative-agency-companion';

// ---------------------------------------------------------------------------
// Images, by file name
// ---------------------------------------------------------------------------

/*
 * colorlib.com's uploads have no year/month folder, so `_wp_attached_file` is
 * the bare file name and `LIKE '%/name'` alone never matches. A file over the
 * size threshold is stored as `name-scaled.jpg`, so accept that too.
 */
$find_image = static function ( $file ) {
	global $wpdb;
	$scaled = preg_replace( '/\.(jpe?g|png)$/i', '-scaled.$1', $file );
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta}
			 WHERE meta_key = '_wp_attached_file'
			   AND ( meta_value = %s OR meta_value = %s OR meta_value LIKE %s OR meta_value LIKE %s )
			 ORDER BY post_id DESC LIMIT 1",
			$file,
			$scaled,
			'%/' . $wpdb->esc_like( $file ),
			'%/' . $wpdb->esc_like( $scaled )
		)
	);
};

// .dev/publish/images/, made by .dev/publish-shots.mjs + .py from a Playground
// with the demo content imported.
$wanted = array(
	'card'         => 'creative-agency-free-design-agency-wordpress-theme.jpg',
	'home'         => 'creative-agency-block-theme-home.jpg',
	'projects'     => 'creative-agency-block-theme-projects.jpg',
	'project-page' => 'creative-agency-block-theme-project-page.jpg',
	'services'     => 'creative-agency-block-theme-services.jpg',
	'contact'      => 'creative-agency-block-theme-contact-form.jpg',
	'palettes'     => 'creative-agency-block-theme-colour-palettes.jpg',
	'dark'         => 'creative-agency-block-theme-dark-mode.jpg',
	'blog'         => 'creative-agency-block-theme-blog.jpg',
);

$img     = array();
$missing = array();
foreach ( $wanted as $key => $file ) {
	$img[ $key ] = $find_image( $file );
	if ( ! $img[ $key ] ) {
		$missing[] = $file;
	}
}

if ( $missing ) {
	echo "ERROR: not in the media library yet, refusing to build a page with gaps:\n  " . implode( "\n  ", $missing ) . "\n";
	return;
}

// vc_btn's `link` attribute is WPBakery's own "url:…|title:…|target:…" encoding.
// It splits on "|" then on the first ":", so a raw URL is cut off at "https:"
// and the button renders href="http://https". Percent-encode anything in a link.
$btn_css = 'display:inline-block !important;vertical-align:middle !important;margin-right:12px !important;margin-bottom:10px !important;';
$tint    = '#f7f9fc';
$accent  = '#0070d8';

/**
 * One WPBakery button. `title` for Download must stay "Download Creative
 * Agency": the email gate keys on the download href, and the button's title is
 * what the page's analytics report.
 */
$button = static function ( $label, $url, $color, $icon, $css_id, $new_tab = true ) use ( $btn_css ) {
	return '[vc_btn title="' . esc_attr( $label ) . '" style="flat" color="' . $color . '"'
		. ' link="url:' . rawurlencode( $url ) . '|title:' . rawurlencode( $label ) . ( $new_tab ? '|target:_blank' : '' ) . '"'
		. ' css=".vc_custom_ca' . $css_id . '{' . $btn_css . '}" i_icon_fontawesome="fa fa-' . $icon . '" add_icon="true"]';
};

$download_label = 'Download Creative Agency';
$main_buttons   = static function ( $n ) use ( $button, $download, $demo, $download_label ) {
	return $button( $download_label, $download, 'green', 'download', $n . 'a' )
		. $button( 'Live demo', $demo, 'grey', 'eye', $n . 'b' );
};

// Look up by slug AND parent: get_page_by_path( 'creative-agency' ) finds only a
// top-level page, and this one is a child of 5091.
$found = get_posts(
	array(
		'post_type'   => 'page',
		'name'        => $slug,
		'post_parent' => $parent,
		// An explicit list, not 'any': in WP_Query 'any' leaves drafts out, so an
		// idempotent script would keep re-creating its own draft.
		'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'numberposts' => 1,
	)
);

$existing = $found ? $found[0] : null;
$page_id  = $existing ? $existing->ID : 0;

// ---------------------------------------------------------------------------
// Content
// ---------------------------------------------------------------------------

$features = array(
	array( 'envelope', 'A contact form without a plugin', 'Checked on the server, protected by a nonce and a honeypot, and it works with JavaScript turned off. One filter hands each message to a CRM, a webhook or a form plugin instead.' ),
	array( 'th-large', 'A page for every project', 'Four case studies in a staggered grid, each opening a page with the client, the service, the year, the story and a link to the next project.' ),
	array( 'paint-brush', 'Six palettes, five type pairings', 'Every palette is measured against WCAG AA before the theme is built, in light mode and in dark, and a palette that fails is not written.' ),
	array( 'moon-o', 'Dark mode', 'A switch for the visitor, separate from the palette you chose. It follows the reader’s system setting until they choose, and lifts the palette rather than replacing it.' ),
	array( 'plug', 'Your form plugin, styled', 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator and Ninja Forms take on the theme’s colours and spacing instead of looking like another website.' ),
	array( 'shopping-cart', 'WooCommerce ready', 'Styled if you install WooCommerce, and it loads nothing at all if you do not.' ),
	array( 'magic', 'Only the motion the design has', 'The header pins and turns black as you scroll, the figures count up, the studio film plays in a popup and the studio photos open in a lightbox. Visitors who ask for reduced motion see no counting.' ),
	array( 'file-text-o', 'A whole site on activation', 'Home, About, Work with four project pages, Services, Blog and Contact are built as ordinary pages you can edit, with a menu that points at them. Nothing happens on a site that already has pages.' ),
	array( 'star-o', 'Icons that follow the palette', 'Service cards and contact details carry Tabler icons drawn in the text colour, so they change with every palette and with dark mode.' ),
);

// Three to a row, each row its own [vc_row]: WPBakery columns are floats, and
// nine thirds in one row snag on the tallest box above them.
$feature_rows = '';
foreach ( array_chunk( $features, 3 ) as $r => $row ) {
	$last          = ( (int) ceil( count( $features ) / 3 ) - 1 === $r );
	$feature_rows .= '[vc_row css=".vc_custom_ca05' . ( $r + 1 ) . '{padding-bottom:' . ( $last ? '40' : '0' ) . 'px !important;}"]';
	foreach ( $row as $f ) {
		list( $icon, $heading, $body ) = $f;
		$feature_rows .= '[vc_column width="1/3"][vcex_icon_box style="two" heading="' . esc_attr( $heading ) . '" heading_type="h3"'
			. ' icon="fa fa-' . $icon . '" icon_color="' . $accent . '" icon_size="28px" heading_size="20px"'
			. ' content_font_size="15px" css=".vc_custom_ca_f_' . sanitize_key( $icon ) . '{margin-bottom:26px !important;}"]'
			. $body . '[/vcex_icon_box][/vc_column]';
	}
	$feature_rows .= '[/vc_row]';
}

$faqs = array(
	array( 'Do I need a plugin?', 'No. The contact form, dark mode and the starter pages are part of the theme, and it needs no page builder. WooCommerce is styled if you add it, and is not required.' ),
	array( 'What is the difference from the Elementor edition?', 'The design is the same. This edition is a block theme: every page, the header, the footer and each section are edited in WordPress’s own Site Editor, with no plugins. The Elementor edition builds its sections with Elementor and the Creative Agency Companion plugin. For a new site, start with this one.' ),
	array( 'Where do the messages go?', 'To the site’s admin email address by default; the <code>creative_agency_contact_email_to</code> filter sends them elsewhere. If you already use a CRM, a webhook or a form plugin, the <code>creative_agency_contact_handlers</code> filter hands each message to it instead, and the theme sends nothing of its own.' ),
	array( 'Which form plugins does it style?', 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator and Ninja Forms are mapped onto the theme’s own colours and spacing.' ),
	array( 'How do I change the video?', 'Select the round play button on the studio photo and change its link to any YouTube address. It opens in a popup, through youtube-nocookie.com. A link to anything else simply opens that page.' ),
	array( 'The logo is a letter in a blue square. How do I change it?', 'The square shows the first letter of your site title, so renaming the site changes it. To use a logo instead, open the header in the Site Editor, select the square and choose an image.' ),
	array( 'Can I change the colours?', 'Six palettes and five type pairings ship with the theme, each a one-click choice under Styles in the Site Editor. Every colour is also a palette entry you can edit there.' ),
	array( 'Can I turn off dark mode, the pinned header or the counting figures?', 'Yes, each with one line in a child theme or a small plugin: <code>add_filter( \'creative_agency_enable_dark_mode\', \'__return_false\' );</code>, <code>add_filter( \'creative_agency_sticky_header\', \'__return_false\' );</code> and <code>add_filter( \'creative_agency_enable_scroll_animations\', \'__return_false\' );</code>. Visitors who ask their system for reduced motion never see the counting either way.' ),
	array( 'Is it translation ready?', 'Yes. Every string is translatable and <code>languages/creative-agency.pot</code> is included.' ),
	array( 'Does it check for updates?', 'Yes, because it is distributed outside the WordPress.org theme directory. The check sends the theme, WordPress and PHP versions, the locale, whether the site is a multisite, and an identifier derived from the site’s address with the site’s own secret key, so it cannot be turned back into the address. The <code>creative_agency_check_for_updates</code> filter switches it off.' ),
);

$toggles = '';
foreach ( $faqs as $faq ) {
	// vcex_toggle takes `heading`: with `title` every toggle renders the
	// shortcode's placeholder. It is styled by its own attributes, not css=.
	$toggles .= '[vcex_toggle heading="' . esc_attr( $faq[0] ) . '" heading_type="h3" heading_font_size="17px" style="boxed" padding_y="16px" padding_x="20px" bottom_margin="12px"]'
		. $faq[1] . '[/vcex_toggle]';
}

$specs = array(
	'Requires'      => 'WordPress 6.6 or newer',
	'PHP'           => '7.4 or newer',
	'Tested up to'  => 'WordPress 7.1',
	'Licence'       => 'GNU General Public License v2 or later',
	'Patterns'      => '26 to insert (17 sections and 9 whole pages), plus 21 the templates and parts use',
	'Templates'     => '10, plus 3 template parts',
	'Starter pages' => '10, built on activation, with a menu',
	'Styles'        => '6 colour palettes × 5 type pairings',
	'Fonts'         => 'Poppins and DM Sans, self-hosted',
	'Icons'         => 'Tabler Icons (MIT), drawn in the text colour',
	'Form plugins'  => 'Contact Form 7, WPForms, Gravity Forms, Fluent Forms, Forminator, Ninja Forms',
	'Build step'    => 'None — no npm, no SCSS',
);

$spec_rows = '';
foreach ( $specs as $label => $value ) {
	$spec_rows .= '<tr><th style="text-align:left;padding:10px 18px 10px 0;border-bottom:1px solid #e3e8f2;font-weight:600;white-space:nowrap;vertical-align:top;">'
		. esc_html( $label ) . '</th><td style="padding:10px 0;border-bottom:1px solid #e3e8f2;">' . $value . '</td></tr>';
}

$section = static function ( $n, $bg, $heading, $text, $image ) use ( $tint ) {
	$background = $bg ? "background-color:{$tint} !important;" : '';
	return "[vc_row css=\".vc_custom_ca{$n}0{padding-top:56px !important;padding-bottom:20px !important;{$background}}\"][vc_column width=\"1/1\"]"
		. '[vcex_heading text="' . esc_attr( $heading ) . '" tag="h2" font_size="34px" text_align="center" bottom_margin="14px" font_weight="700"]'
		. "[vc_column_text css=\".vc_custom_ca{$n}1{text-align:center !important;max-width:790px !important;margin-left:auto !important;margin-right:auto !important;}\"]{$text}[/vc_column_text]"
		. "[/vc_column][/vc_row][vc_row css=\".vc_custom_ca{$n}2{padding-bottom:56px !important;{$background}}\"][vc_column width=\"1/1\"]"
		. "[vcex_image image_id=\"{$image}\" align=\"center\" border_radius=\"12px\" bottom_margin=\"0px\"][/vc_column][/vc_row]";
};

// Two versions, side by side: one [vc_row], two halves.
$versions = "[vc_row css=\".vc_custom_ca009{padding-top:56px !important;padding-bottom:16px !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="Two versions of Creative Agency" tag="h2" font_size="34px" text_align="center" bottom_margin="10px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_ca0091{text-align:center !important;max-width:720px !important;margin-left:auto !important;margin-right:auto !important;}"]The same design, built two ways. Both are free.[/vc_column_text]'
	. '[/vc_column][/vc_row]'
	. "[vc_row css=\".vc_custom_ca010{padding-bottom:56px !important;}\"]"
	. '[vc_column width="1/2" css=".vc_custom_ca0101{padding:28px 30px 18px !important;background-color:' . $tint . ' !important;border-radius:12px !important;}"]'
	. '[vcex_heading text="Block theme" tag="h3" font_size="24px" bottom_margin="12px" font_weight="700"]'
	. '[vc_column_text]<p>This download. Everything is edited in WordPress’s own Site Editor, with no plugins: the header, the footer, every template and every section. Patterns for each section, six colour palettes, five type pairings and a dark mode for visitors.</p><p><strong>Recommended for new sites.</strong></p>[/vc_column_text]'
	. '[vc_column_text css=".vc_custom_ca0102{margin-top:8px !important;}"]' . $main_buttons( '010' ) . '[/vc_column_text]'
	. '[/vc_column]'
	. '[vc_column width="1/2" css=".vc_custom_ca0103{padding:28px 30px 18px !important;background-color:' . $tint . ' !important;border-radius:12px !important;}"]'
	. '[vcex_heading text="Elementor edition" tag="h3" font_size="24px" bottom_margin="12px" font_weight="700"]'
	. '[vc_column_text]<p>The same design built for Elementor, for sites that already use it. It needs the free Elementor plugin and the <a href="' . esc_url( $companion_repo ) . '" target="_blank" rel="noopener">Creative Agency Companion</a> plugin, which adds its widgets. The source is free on GitHub.</p>[/vc_column_text]'
	. '[vc_column_text css=".vc_custom_ca0104{margin-top:8px !important;}"]'
	. $button( 'Elementor demo', $elementor_demo, 'grey', 'eye', '010c' )
	. $button( 'Source on GitHub', $elementor_repo, 'grey', 'github', '010d' )
	. '[/vc_column_text]'
	. '[/vc_column][/vc_row]';

$content = "[vc_row css=\".vc_custom_ca001{padding-top:64px !important;padding-bottom:40px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="A design studio theme with a page for every project" tag="h2" font_size="46px" text_align="center" bottom_margin="20px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_ca002{text-align:center !important;font-size:18px !important;max-width:820px !important;margin-left:auto !important;margin-right:auto !important;}"]'
	. 'Creative Agency is a free block theme for design studios, creative agencies and freelancers. One large statement per screen, a staggered grid of projects that each get their own page, a black services band with client logos, counting figures, a strip of studio photographs and a contact form that needs no plugin. Six colour palettes, a visitor dark mode and full site editing throughout.'
	. '[/vc_column_text][vc_column_text css=".vc_custom_ca003{text-align:center !important;margin-top:26px !important;}"]' . $main_buttons( '004' ) . '[/vc_column_text]'
	. "[/vc_column][/vc_row][vc_row css=\".vc_custom_ca006{padding-top:0px !important;padding-bottom:64px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. "[vcex_image image_id=\"{$img['home']}\" align=\"center\" border_radius=\"14px\" bottom_margin=\"0px\"][/vc_column][/vc_row]\n\n"

	. $versions . "\n\n"

	. $section( '01', true, 'Your work, one project at a time',
		'Four projects in the design’s staggered grid, each with a photograph, a title on a yellow rule and a link to its own page. Add a fifth by duplicating one: it is ordinary blocks.',
		$img['projects'] ) . "\n\n"

	. $section( '02', false, 'A page for every project',
		'Each case study opens with the photograph, then the client, the service and the year, the story of the project and what it achieved, and a link to the previous and next project. All four are built for you on activation.',
		$img['project-page'] ) . "\n\n"

	. $section( '03', true, 'Services, figures and client logos',
		'A black band of services with icons that take the palette’s colours, a row of client logos, and figures that count up as they scroll into view. Each is a pattern: insert it, change the words, and it stays as editable as a paragraph.',
		$img['services'] ) . "\n\n"

	. $section( '04', false, 'A contact form without a plugin',
		'The contact form is part of the theme. It checks every field on the server, carries a nonce and a honeypot, and works with JavaScript turned off. Messages go to the site’s admin address — or, with one filter, to whatever CRM, webhook or form plugin you already use.',
		$img['contact'] ) . "\n\n"

	. $section( '05', true, 'Six palettes, checked before release',
		'Azure is the design’s own palette; Violet, Coral, Emerald and Ink restyle it, and Midnight is a dark palette. Every one is measured against WCAG AA before the theme is built, in light mode and in dark, and a palette that fails is not written.',
		$img['palettes'] ) . "\n\n"

	. $section( '06', false, 'Dark mode the visitor controls',
		'The switch sits in the header, beside “Say hi”. It follows the reader’s system setting until they choose for themselves, and it lifts your palette rather than replacing it.',
		$img['dark'] ) . "\n\n"

	. "[vc_row css=\".vc_custom_ca050{padding-top:56px !important;padding-bottom:16px !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="What you get" tag="h2" font_size="34px" text_align="center" bottom_margin="34px" font_weight="700"][/vc_column][/vc_row]'
	. $feature_rows . "\n\n"

	. $section( '07', true, 'A journal, with a sidebar',
		'Studio notes, process write-ups, case notes. The blog, single posts, categories, tags, search and a 404 are all designed rather than inherited, with the design’s sidebar of search, categories, recent posts and tags.',
		$img['blog'] ) . "\n\n"

	. "[vc_row css=\".vc_custom_ca070{padding-top:56px !important;padding-bottom:18px !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="Questions" tag="h2" font_size="34px" text_align="center" bottom_margin="28px" font_weight="700"][/vc_column][/vc_row]'
	. "[vc_row css=\".vc_custom_ca071{padding-bottom:48px !important;}\"][vc_column width=\"1/1\"]{$toggles}[/vc_column][/vc_row]\n\n"

	. "[vc_row css=\".vc_custom_ca080{padding-top:48px !important;padding-bottom:56px !important;background-color:{$tint} !important;}\"][vc_column width=\"1/1\"]"
	. '[vcex_heading text="The details" tag="h2" font_size="34px" text_align="center" bottom_margin="26px" font_weight="700"]'
	. '[vc_column_text css=".vc_custom_ca081{max-width:680px !important;margin-left:auto !important;margin-right:auto !important;}"]<table style="width:100%;border-collapse:collapse;">' . $spec_rows . '</table>[/vc_column_text]'
	. '[vc_column_text css=".vc_custom_ca082{text-align:center !important;margin-top:34px !important;}"]' . $main_buttons( '083' ) . '[/vc_column_text]'
	. '[/vc_column][/vc_row]';

// ---------------------------------------------------------------------------
// Save
// ---------------------------------------------------------------------------

// kses strips the shortcode attributes this page is made of.
$kses = has_filter( 'content_save_pre', 'wp_filter_post_kses' );
if ( $kses ) {
	kses_remove_filters();
}

$args = array(
	'post_title'   => $title,
	'post_name'    => $slug,
	'post_content' => $content,
	// Draft on creation, but never demote a page that is already published.
	'post_status'  => $existing ? $existing->post_status : 'draft',
	'post_type'    => 'page',
	'post_parent'  => $parent,
);

if ( $page_id ) {
	$args['ID'] = $page_id;
	$result     = wp_update_post( wp_slash( $args ), true );
} else {
	$result  = wp_insert_post( wp_slash( $args ), true );
	$page_id = is_wp_error( $result ) ? 0 : $result;
}

if ( $kses ) {
	kses_init_filters();
}

if ( is_wp_error( $result ) ) {
	echo 'ERROR: ' . $result->get_error_message() . "\n";
	return;
}

set_post_thumbnail( $page_id, $img['card'] );

// The full-width template and WPBakery's own flag, as Unioncorp and Pato have.
update_post_meta( $page_id, '_wp_page_template', 'templates/no-sidebar.php' );
update_post_meta( $page_id, '_wpb_vc_js_status', 'true' );
update_post_meta( $page_id, '_yoast_wpseo_title', 'Creative Agency – Free Design Agency WordPress Theme - %%sitename%%' );
update_post_meta( $page_id, '_yoast_wpseo_metadesc', 'A free WordPress block theme for design studios and creative agencies, with a page for every project, a contact form built in, six colour palettes and dark mode.' );

// WPBakery keeps every css="…" rule in _wpb_shortcodes_custom_css and only
// regenerates it when the page is saved through the builder UI.
if ( function_exists( 'visual_composer' ) && method_exists( visual_composer(), 'buildShortcodesCss' ) ) {
	visual_composer()->buildShortcodesCss( $page_id, 'custom' );
	visual_composer()->buildShortcodesCss( $page_id, 'default' );
	echo "custom css rebuilt\n";
} else {
	echo "WARNING: could not rebuild the WPBakery custom css\n";
}

$saved = get_post_field( 'post_content', $page_id );

echo 'page: ' . $page_id . ' (' . get_post_status( $page_id ) . '), parent ' . wp_get_post_parent_id( $page_id ) . "\n";
echo 'images: ' . wp_json_encode( $img ) . "\n";
echo 'length: ' . strlen( $saved ) . "\n";
echo 'icon boxes: ' . substr_count( $saved, '[vcex_icon_box' ) . " (must be 9)\n";
echo 'toggles: ' . substr_count( $saved, '[vcex_toggle heading=' ) . ' (must be ' . count( $faqs ) . ")\n";
echo 'section images: ' . substr_count( $saved, '[vcex_image' ) . " (must be 8)\n";
echo 'download buttons: ' . substr_count( $saved, 'title="' . $download_label . '"' ) . " (must be 3: hero, block-theme column, details)\n";
echo 'download hrefs: ' . substr_count( $saved, rawurlencode( $download ) ) . " (must be 3)\n";
echo 'documentation buttons: ' . substr_count( $saved, 'title="Documentation"' ) . " (must be 0)\n";
echo 'half columns: ' . substr_count( $saved, '[vc_column width="1/2"' ) . " (must be 2)\n";
echo 'unbalanced rows: ' . ( substr_count( $saved, '[vc_row' ) - substr_count( $saved, '[/vc_row]' ) ) . "\n";
