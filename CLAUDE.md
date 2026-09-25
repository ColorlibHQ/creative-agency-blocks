# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**Creative Agency 1.0.0** is a Colorlib **WordPress block theme** (Full Site
Editing) for a design and development studio. Text domain and slug
`creative-agency`. No companion plugin, no page builder, no jQuery, no build
step for the shipped files. Self-hosted: `Update URI` in `style.css` points at
`updates.colorlib.com`, like Unioncorp, Pato, Academia and Unapp.

**The design is Colorlib's `creativeagency2` HTML template**
(`preview.colorlib.com/theme/creativeagency2/`), the one the Elementor demo at
`colorlibhub.com/creative-agency/` was built from — NOT `/theme/creative-agency/`
(a Bootstrap 3 one-pager, "We Are Creative Agency") and not `/theme/creativeagency/`
("We Provide Solutions that Brings Joy"). Compare against creativeagency2.

The toolchain was ported from Unioncorp (`~/Fresh Projects/unioncorp-blocks`).

## Commands

```bash
python3 .dev/build_theme.py        # theme.json + styles/** (audits contrast first)
python3 .dev/build_patterns.py     # patterns/*.php (never edit those by hand)
node .dev/build-fonts.mjs          # assets/fonts (Poppins 300-900, DM Sans; latin + latin-ext)

# A throwaway WordPress — this theme's port is 9492
npx -y @wp-playground/cli@3.1.54 server --port=9492 --php=8.3 --wp=latest \
  --mount-before-install="$PWD:/wordpress/wp-content/themes/creative-agency" \
  --blueprint=.dev/blueprint.json --login

export WP_URL=http://127.0.0.1:9492 WP_USER=admin WP_PASS=password
node .dev/normalize-blocks.mjs     # ALWAYS after build_patterns.py
node .dev/validate-blocks.mjs      # templates, parts, patterns, stored pages, menus
curl -b playground_auto_login_already_happened=1 $WP_URL/wp-content/themes/creative-agency/.dev/refresh-pages.php
bash .dev/check-all.sh             # contrast light+dark, overflow, alignment, buttons, dead selectors
node .dev/editor-check.mjs         # every pattern in the real editor canvas
bash .dev/compare.sh <name> <template page> <theme path> [width]   # side by side → .dev/compare/
bash .dev/build-zip.sh [outdir]    # prints the zip path (a fresh mktemp dir by default)
```

`node` resolves Playwright from the script's directory: `node_modules` is a
symlink to `~/Fresh Projects/tailwind-templates/node_modules` (gitignored).
The Playground auto-logs-in every new browser; send the cookie
`playground_auto_login_already_happened=1` to see the site as a visitor.

## Architecture

| Concern | Where |
| --- | --- |
| Palette (16 slugs × 6 variations), type pairings, spacing, fonts | `.dev/build_theme.py` → `theme.json`, `styles/**` |
| Sections, pages, headings, hidden template pieces (47 patterns) | `.dev/build_patterns.py` + `.dev/patternlib.py` → `patterns/` |
| Templates (10) and parts (header, footer, sidebar) | `templates/`, `parts/` (parts are one `wp:pattern` each) |
| Starter pages + menu on activation, link fixing | `inc/front-page-setup.php` |
| Contact form (shortcode, honeypot, nonce, redirect field) | `inc/contact-form.php`, `assets/css/forms.css` |
| Dark mode | `inc/scheme.php`, `assets/css/scheme.css`, `assets/js/scheme-toggle.js` |
| Sticky header, counting figures, video popup | `assets/js/interactions.js` |
| Everything theme.json cannot say, and every block style | `style.css` |
| Self-hosted updates | `inc/updates.php` |

## Conventions that matter

- **Palette slugs are jobs, not colours.** `overlay` (text on photos/black),
  `on-dark`, `on-primary`, `accent` (decoration only), `highlight` (the yellow
  bars, decoration only), `decor` (the mint triangle), `secondary`/`tertiary`
  (the violet and orange figures — large text, audited at 3:1). Anything on a
  ground other than `base` needs its own slug. `build_theme.py` refuses a
  stylesheet that reads a slug the palette lacks.
- **Never alias a palette variable into a custom property on :root.** Dark mode
  computes the lifted colours on `<html>` and applies them on `<body>`
  (`scheme.css`); an alias resolved on the root keeps the light value.
- **Sections are alignfull groups with top/bottom padding only**, from the
  spacing scale. 80 = the design's 150px, 90 = its 200px (both fall to 60px on
  a phone). `sp()` refuses anything else except `"0"`.
- **A group with a background and no padding gets core's 1.25em.** State zero.
- **Resetting a child's `margin` kills the layout gap.** Reset `margin-inline` /
  `margin-bottom`, never `margin`, on anything inside a flow/constrained group
  (this bit the contact details and the project separator).
- **The editor gives every block `position: relative`** with a stronger
  selector, so anything absolutely positioned (the watermark words) needs
  `!important` or it drops into the flow while editing.
- **Theme-owned things the editor must draw live in style.css** (the dark mode
  switch was an empty blue pill in the editor while its CSS lived in scheme.css).
- **Icons are classes on the block** (`creative-agency-icon--<name>`), drawn by
  a mask in the text colour; every name needs a rule (dead-selectors checks).
- **The watermark words** ("Projects", "Services", "Quick Fact") are paragraphs
  with the Watermark block style, `aria-hidden` via a render filter, hidden
  below 992px as the design does. The contrast check skips aria-hidden text.
- **The brand tile** shows the first letter of the site title (`::first-letter`
  on a zero-size title); a Site Logo replaces it. It is a logotype, exempt
  from the contrast check.
- **Starter pages are copies.** After a pattern change run refresh-pages.php
  before believing any rendered check. Activation removes kses around the
  inserts (theme content only) so the map's `<iframe>` survives for a site
  admin without `unfiltered_html`, and rewrites `home_url('/work/')`-style links
  to real permalinks.
- **Links in patterns** use `home_url()`; PHP in a pattern runs at registration.
- **Line breaks in statements:** put the space BEFORE `<br>` (`… <br>studio`),
  never after: `<br>` is hidden on phones and a space after it shows as a
  leading space in the editor.
- **Grep before release:** `grep -ri "unioncorp\|pato\|enquiry\|reservation\|flame"`
  must find nothing outside `.dev/` history notes.

## Theme Check

Run on the built zip (`.dev/build-zip.sh`), with the theme-check plugin in a
Playground. Three REQUIRED findings are expected and documented in readme.txt:
`Update URI`, `add_shortcode()` (the contact form must survive pattern
expansion), and the Unsplash licence of the photographs. 0 warnings otherwise.
