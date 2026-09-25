# Creative Agency

A block theme for design studios, creative agencies and freelancers.

A free WordPress block theme by [Colorlib](https://colorlib.com/). Full site editing,
no page builder and no plugins required.

- **Theme page:** https://colorlib.com/wp/themes/creative-agency/
- **Live demo:** https://colorlibhub.com/creative-agency-blocks/
- **Download:** https://updates.colorlib.com/download/theme/creative-agency.zip (or the zip attached to the [latest release](../../releases/latest))

## Description

Creative Agency is a full site editing theme for a design and development studio: a large opening statement, a staggered grid of projects with a page for each one, a black services band with client logos, figures that count up, a strip of studio photographs, a blog with a sidebar, and a contact form that needs no plugin.

On activation it builds the whole site — home, about, work with four project pages, services, blog and contact — as ordinary pages you can edit, and a menu that points at them. It does nothing if the site already has pages.

Six colour palettes and five type pairings, each checked for contrast before release rather than by eye. Visitor-facing dark mode that follows the reader's system setting until they choose for themselves. WooCommerce is styled if you install it and loads nothing if you do not.

Everything is editable in the Site Editor: the header, the footer, every template and every section. Nothing here depends on a page builder.

## Two versions

This repository is the **block theme**. The same design also exists as an
**Elementor edition** for sites built with Elementor: [live demo](https://colorlibhub.com/creative-agency/),
[source](https://github.com/ColorlibHQ/creative-agency). It needs the free Elementor plugin. It also needs the [Creative Agency Companion](https://github.com/ColorlibHQ/creative-agency-companion) plugin. For a new site
the block theme is the one to use.

## Installation

1. In WordPress, go to Appearance → Themes → Add New Theme → Upload Theme.
2. Choose creative-agency.zip and click Install Now, then Activate.
3. Appearance → Editor is where the header, footer, colours and templates live.

The theme updates itself from colorlib.com: it is distributed outside the
WordPress.org directory, so it checks `updates.colorlib.com` for new versions.

## Development

The files in `.dev/` generate and check the theme (palettes, patterns, block
validation, rendered contrast, overflow and alignment checks) and build the zip.
They are not part of the distributed theme. See `.dev/README.md` where present,
and `CLAUDE.md` for the conventions.

## Licence

GNU General Public License v2 or later. Photographs and fonts carry their own
licences, listed in `readme.txt`.
