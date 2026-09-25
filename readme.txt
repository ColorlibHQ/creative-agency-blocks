=== Creative Agency ===

Contributors: colorlib
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: portfolio, blog, full-site-editing, block-patterns, block-styles, template-editing, wide-blocks, accessibility-ready, translation-ready, custom-colors, custom-menu, custom-logo, featured-images, threaded-comments, one-column, two-columns, right-sidebar, sticky-post, theme-options

A block theme for design studios, creative agencies and freelancers.

== Description ==

Creative Agency is a full site editing theme for a design and development
studio: a large opening statement, a staggered grid of projects with a page
for each one, a black services band with client logos, figures that count up,
a strip of studio photographs, a blog with a sidebar, and a contact form that
needs no plugin.

On activation it builds the whole site — home, about, work with four project
pages, services, blog and contact — as ordinary pages you can edit, and a menu
that points at them. It does nothing if the site already has pages.

Six colour palettes and five type pairings, each checked for contrast before
release rather than by eye. Visitor-facing dark mode that follows the reader's
system setting until they choose for themselves. WooCommerce is styled if you
install it and loads nothing if you do not.

Everything is editable in the Site Editor: the header, the footer, every
template and every section. Nothing here depends on a page builder.

== Installation ==

1. In WordPress, go to Appearance → Themes → Add New Theme → Upload Theme.
2. Choose creative-agency.zip and click Install Now, then Activate.
3. Appearance → Editor is where the header, footer, colours and templates live.

== Frequently Asked Questions ==

= Do I need a plugin for the contact form? =

No. The form is part of the theme and sends with WordPress's own wp_mail() to
the site's admin email address. If your host cannot send mail, install any SMTP
plugin — whatever fixes a lost password-reset email fixes the form too.

= The logo is a letter in a blue square. How do I change it? =

The square shows the first letter of your site title, so renaming the site
under Settings → General changes it. To use a logo instead, open the header in
Appearance → Editor, click the blue square (it holds the Site Logo block) and
choose an image: the logo takes the square's place.

= How do I change the colours? =

Appearance → Editor → Styles → Browse styles. Six palettes are included, and
each one restyles every section. To change a single colour, open Styles →
Colors → Edit palette.

= Why is the brand blue not used for links and the band? =

The design's blue, #00a7ff, is 2.6:1 against white, which fails WCAG AA for
text. It ships as the decorative accent (the navigation bar, the focus ring)
and a deeper blue carries links, buttons and the "Visit our work" band.

= How do I change an icon? =

Select the block, open Advanced → Additional CSS class(es) and change the
`creative-agency-icon--…` class to another one the theme ships: user-search,
palette, code, vector-bezier, package, components, map-pin, device-mobile,
mail.

= Can I turn dark mode off? =

Yes: add add_filter( 'creative_agency_enable_dark_mode', '__return_false' ); to
a child theme or a small plugin.

= Can I stop the header following the page, or the figures counting? =

add_filter( 'creative_agency_sticky_header', '__return_false' ); keeps the
header at the top of the page. add_filter(
'creative_agency_enable_scroll_animations', '__return_false' ); shows the
figures at their final value. Visitors who have asked their system for reduced
motion never see the counting either way.

= How do I change the video? =

Select the round play button on the studio photograph and change its link to
any YouTube address. It opens in a popup through youtube-nocookie.com. The
demo links Google Design's "Making Material Design"; replace it with your own
studio's film.

= Does the theme contact any server? =

Only to check for its own updates, because it is distributed outside the
WordPress.org directory. The check goes to updates.colorlib.com and sends the
theme version, the WordPress and PHP versions, the locale, whether the site is
a multisite, and a one-way hash of the site address keyed with the site's own
secret. No personal data and no site name. add_filter(
'creative_agency_check_for_updates', '__return_false' ); turns it off.

== Theme Check ==

Theme Check reports three REQUIRED findings and no warnings. All three are
deliberate, and each is the price of something the theme does on purpose.

1. **add_shortcode() in inc/contact-form.php.** The contact form has to keep
   working after a pattern is expanded into a page's content, where PHP never
   runs. A shortcode is the only mechanism WordPress offers for that. Moving it
   to a plugin would mean the form stops working the moment the plugin is
   disabled, on a page the theme built.
2. **Unsplash and Pexels photographs.** The demo images are under the
   Unsplash and Pexels licences, which are not GPL-compatible. Each is credited
   below. Replace them with your own and the finding goes with them.
3. **Update URI in style.css.** This theme is distributed outside the
   WordPress.org directory and checks colorlib.com for its own updates. A theme
   inside the directory must not carry this header.

== Copyright ==

Creative Agency WordPress Theme, (C) 2026 Colorlib.
Creative Agency is distributed under the terms of the GNU GPL v2 or later.

Poppins and DM Sans
License: SIL Open Font License 1.1
Source: https://fontsource.org/

Tabler Icons
License: MIT, https://github.com/tabler/tabler-icons/blob/main/LICENSE
Source: https://tabler.io/icons

Client logos (assets/images/clients/)
License: GPL-2.0-or-later, drawn for this theme.

Photographs (assets/images/*.webp)
Every photograph was traced to its original page. Unsplash License,
https://unsplash.com/license, unless marked Pexels License,
https://www.pexels.com/license/.
* product-phone.webp: Daniel Korpai,
  https://unsplash.com/photos/space-gray-iphone-x-displaying-the-most-inspiring-places-bOKIptPzdPk
* work-arcade.webp, work-arcade-card.webp: David Švihovec,
  https://unsplash.com/photos/black-smartphone-e-AB_mUpCK8
* work-whisk.webp: Isabella Fischer,
  https://unsplash.com/photos/multicolored-whip-with-cream-8bqHLkhArtc
* work-harrow.webp: Avtar Singh,
  https://unsplash.com/photos/amber-glass-dropper-bottle-with-blank-label-on-concrete-block-MkuwnDkT1s0
* work-nookdesk.webp, studio-4.webp: Elvis (Pexels License),
  https://www.pexels.com/photo/photo-of-a-laptop-and-a-tablet-on-the-table-2528118/
* studio-1.webp: Sebastien Bonneval,
  https://unsplash.com/photos/man-in-gray-shirt-facing-sticky-notes-UIpFY1Umamw
* studio-2.webp: Jason Goodman,
  https://unsplash.com/photos/man-smiling-in-room-fXVx1opWGxM
* studio-3.webp: Jason Goodman, unsplash.com/photos/vbxyFxlgpjM. The
  photographer has since withdrawn it from Unsplash; the copy here was taken
  while it was published under the Unsplash License, as archived at
  https://web.archive.org/web/20231003075413/https://unsplash.com/photos/vbxyFxlgpjM
* studio-5.webp, studio-meeting.webp: Jason Goodman,
  unsplash.com/photos/MUZFKa_mttU, withdrawn in the same way; archived at
  https://web.archive.org/web/20220811094218/https://unsplash.com/photos/MUZFKa_mttU

== Changelog ==

= 1.0.0 =
* Initial release.
