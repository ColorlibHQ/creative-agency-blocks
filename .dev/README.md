# Build tooling

Nothing in `patterns/`, `theme.json` or `styles/` is written by hand. Edit the
generator, run it, and commit what it produces.

```bash
python3 .dev/build_theme.py        # theme.json + styles/colors/* + styles/typography/*
python3 .dev/build_patterns.py     # patterns/*.php
node    .dev/build-fonts.mjs       # assets/fonts/*.woff2 (only when the weights change)
```

## The order that matters

Generated block markup is a guess until the editor has seen it. Block comment
attributes must match what a block's `save()` writes, and when they do not, the
editor shows "this block contains unexpected or invalid content" — while the
front end looks perfect and nothing warns you at build time.

So, against a WordPress with this theme active:

```bash
export WP_URL=http://127.0.0.1:9492 WP_USER=admin WP_PASS=password
node .dev/normalize-blocks.mjs     # re-serialise every pattern as the editor would
node .dev/validate-blocks.mjs      # then fail on anything still invalid
```

`normalize-blocks.mjs` stashes the `<?php … ?>` snippets that carry image URLs
before parsing and puts them back afterwards. **Identical snippets must share a
token**: a cover block names the same `get_theme_file_uri()` call twice, and two
different tokens make the attribute and the markup disagree, which parses as
invalid.

A throwaway WordPress to run them against:

```bash
npx -y @wp-playground/cli@3.1.54 server --port=9492 --php=8.3 --wp=latest \
  --mount-before-install="$PWD:/wordpress/wp-content/themes/creative-agency" \
  --blueprint=.dev/blueprint.json --login
```

## The other checks

After a pattern changes, the running site still shows the OLD starter pages
(they are copies made at activation). Rebuild them the way activation does:

```bash
curl -b playground_auto_login_already_happened=1 \
  http://127.0.0.1:9492/wp-content/themes/creative-agency/.dev/refresh-pages.php
```

Then:

```bash
bash .dev/check-all.sh             # contrast (light + dark) on 14 pages, overflow,
                                   # alignment, button boundaries (both schemes), dead selectors
node .dev/editor-check.mjs         # every pattern opened in the editor: invalid blocks,
                                   # "Type / to choose a block" placeholders, icons not drawn
python3 .dev/dead-selectors.py     # CSS classes nothing emits, block styles nothing styles
bash .dev/compare.sh home index.html / 1440   # template and theme side by side, .dev/compare/
node .dev/palettes.mjs <url> <dir> [dark]     # a page under every palette
node .dev/screenshot.mjs http://127.0.0.1:9492/ screenshot.png
bash .dev/build-zip.sh             # the distributable, without .dev or node_modules
```

`build_theme.py` audits every palette before writing and **refuses to emit one
that fails WCAG AA** on any foreground/background pair the design produces
(4.5:1, and 3:1 for the 50px figures, which are large text). It also refuses
any stylesheet that reads a palette slug the palette does not define.

The template's brand colours are decorative for this reason. `#00a7ff` is
2.63:1 on white and the counter orange `#fd8e5e` is 2.28:1; the blue ships as
`accent`, a deeper `primary` (#0070d8) carries links, buttons and the band, and
`tertiary` is an orange deep enough for 3:1 at 50px.

## Spacing and colour are vocabularies, not values

`sp()` refuses any spacing step that is not on the registered scale, because an
undefined preset variable makes WordPress drop the whole declaration and the
element silently falls back to its inherited gap. The same applies to colour
slugs: patterns name `primary` or `surface`, never a hex value, which is what
lets all six palettes restyle every section.
