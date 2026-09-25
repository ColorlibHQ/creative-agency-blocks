#!/usr/bin/env python3
"""
Generates theme.json and every style variation from one table of colours.

The design's signature blue, #00a7ff, is 2.63:1 on white. It carries the nav
underline, the focus ring and other decoration, and fails AA for anything a
reader has to read: text, links, the "Visit our work" band's label. So the
palette keeps it as `accent` and derives `primary` (#0070d8, the template's own
logo blue taken down until it clears 4.5:1 on both page grounds) for
everything readable. Its orange counter figure (#fd8e5e) is 2.28:1 even at
50px, so `tertiary` is a deeper orange that clears the 3:1 large text needs.
The yellow bars (#ffcb00) are `highlight`, decoration only, never text.

Nothing here is eyeballed: audit() computes every pair the design actually
produces and refuses to write a palette that fails, and button_text() checks
that each palette's `on-primary` is the best label on offer.

Usage:  python3 .dev/build_theme.py
"""

import collections
import json
import os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
os.chdir(ROOT)

# ---------------------------------------------------------------------------
# Palette
# ---------------------------------------------------------------------------
# Fifteen slugs, the same in every variation, so a pattern written against them
# works under all of them. `overlay` is separate from `base` on purpose: text on
# a photograph or on the black `dark` ground must stay near-white even when the
# palette is dark, and writing it as `base` is what turns a dark palette's
# covers black-on-black. `on-dark` and `on-primary` exist for the same reason:
# any colour that sits on a ground other than `base` needs its own slug.
PALETTE = [
    ("Base",         "base",         "#ffffff"),
    ("Surface",      "surface",      "#fbf9ff"),   # the template's lavender sidebar ground
    ("Contrast",     "contrast",     "#1f1f1f"),   # the template's heading colour
    ("Muted",        "muted",        "#6b6b6b"),   # body copy; the template's #919191 is 3.15:1
    ("Primary",      "primary",      "#0070d8"),   # readable blue: links, buttons, the band
    ("Primary deep", "primary-deep", "#0059ad"),
    ("Accent",       "accent",       "#00a7ff"),   # the design's blue, decorative only
    ("Highlight",    "highlight",    "#ffcb00"),   # the yellow underline bars, decorative only
    ("Secondary",    "secondary",    "#615cfd"),   # violet: first counter, third service icon
    ("Tertiary",     "tertiary",     "#e9652d"),   # orange: third counter, second service icon
    ("Decor",        "decor",        "#00d264"),   # the mint triangle in the opening band
    ("Dark",         "dark",         "#000000"),   # services band and footer
    ("Divider",      "divider",      "#e2e2e2"),
    ("Overlay",      "overlay",      "#ffffff"),
    ("On dark",      "on-dark",      "#c7c7c7"),
    ("On primary",   "on-primary",   "#ffffff"),
]

COLOR_SETS = {
    # The template's own colours.
    "colors-1-azure": ("Azure", {
        "base": "#ffffff", "surface": "#fbf9ff", "contrast": "#1f1f1f", "muted": "#6b6b6b",
        "primary": "#0070d8", "primary-deep": "#0059ad", "accent": "#00a7ff", "highlight": "#ffcb00",
        "secondary": "#615cfd", "tertiary": "#e9652d", "decor": "#00d264", "dark": "#000000", "divider": "#e2e2e2",
        "overlay": "#ffffff", "on-dark": "#c7c7c7", "on-primary": "#ffffff",
    }),
    "colors-2-violet": ("Violet", {
        "base": "#ffffff", "surface": "#f8f6ff", "contrast": "#1c1830", "muted": "#625d74",
        "primary": "#5b3fd6", "primary-deep": "#4527b8", "accent": "#8a6cff", "highlight": "#ffcb00",
        "secondary": "#0b72c9", "tertiary": "#d1406e", "decor": "#00c2a8", "dark": "#130f24", "divider": "#e4e0f2",
        "overlay": "#ffffff", "on-dark": "#c9c3dc", "on-primary": "#ffffff",
    }),
    "colors-3-coral": ("Coral", {
        "base": "#ffffff", "surface": "#fff8f5", "contrast": "#26170f", "muted": "#6e6159",
        "primary": "#c2410c", "primary-deep": "#9a330a", "accent": "#ff7a45", "highlight": "#ffd23f",
        "secondary": "#6d3fd0", "tertiary": "#0b7fb3", "decor": "#2bb673", "dark": "#1d120c", "divider": "#f0e1d8",
        "overlay": "#ffffff", "on-dark": "#d9c9bf", "on-primary": "#ffffff",
    }),
    "colors-4-emerald": ("Emerald", {
        "base": "#ffffff", "surface": "#f4fbf8", "contrast": "#10231b", "muted": "#566960",
        "primary": "#047857", "primary-deep": "#065f46", "accent": "#10d39a", "highlight": "#ffcb00",
        "secondary": "#2563eb", "tertiary": "#c0357a", "decor": "#ffcb00", "dark": "#06140f", "divider": "#d9ebe3",
        "overlay": "#ffffff", "on-dark": "#bcd3c9", "on-primary": "#ffffff",
    }),
    # Black, white and the yellow bars: the design with its colour taken out.
    "colors-5-ink": ("Ink", {
        "base": "#ffffff", "surface": "#f6f6f6", "contrast": "#111111", "muted": "#636363",
        "primary": "#111111", "primary-deep": "#3a3a3a", "accent": "#666666", "highlight": "#ffcb00",
        "secondary": "#444444", "tertiary": "#5c5c5c", "decor": "#bdbdbd", "dark": "#000000", "divider": "#e0e0e0",
        "overlay": "#ffffff", "on-dark": "#bdbdbd", "on-primary": "#ffffff",
    }),
    # A dark palette: `base` is the page, so it is dark here, and a button is a
    # bright fill whose label is the DARK colour — the opposite of the usual
    # rule, which is why the audit measures instead of assuming.
    "colors-6-midnight": ("Midnight", {
        "base": "#0f1115", "surface": "#171a21", "contrast": "#f2f3f5", "muted": "#a7aebb",
        "primary": "#5cb8ff", "primary-deep": "#8fd0ff", "accent": "#00a7ff", "highlight": "#ffcb00",
        "secondary": "#a09cff", "tertiary": "#ff9a6b", "decor": "#00d264", "dark": "#050608", "divider": "#272b35",
        "overlay": "#ffffff", "on-dark": "#a7aebb", "on-primary": "#050608",
    }),
}

# Typography. Poppins is the template's only face; DM Sans is the second family
# a pairing can bring in, and the system stack costs nothing to download.
TYPE_SETS = {
    "type-1-poppins": ("Poppins throughout", "poppins", "poppins"),
    "type-2-poppins-dm-sans": ("Poppins headings, DM Sans text", "poppins", "dm-sans"),
    "type-3-dm-sans": ("DM Sans throughout", "dm-sans", "dm-sans"),
    "type-4-dm-sans-poppins": ("DM Sans headings, Poppins text", "dm-sans", "poppins"),
    "type-5-system": ("System fonts", "system", "system"),
}

LATIN = ("U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, "
         "U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD")
LATIN_EXT = ("U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, "
             "U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, "
             "U+2C60-2C7F, U+A720-A7FF")

# Must match FAMILIES in .dev/build-fonts.mjs.
FAMILIES = collections.OrderedDict([
    ("poppins", ("Poppins", "Poppins, system-ui, -apple-system, 'Segoe UI', sans-serif",
                 "poppins", ["300", "400", "500", "600", "700", "900"])),
    ("dm-sans", ("DM Sans", "'DM Sans', system-ui, -apple-system, 'Segoe UI', sans-serif",
                 "dm-sans", ["400", "500", "700"])),
    ("system", ("System", "system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif", None, [])),
])


def fluid(minimum, maximum):
    return collections.OrderedDict([("min", minimum), ("max", maximum)])


# The template's sizes at desktop, and at phone width where it sets one.
FONT_SIZES = [
    ("Small",    "small",    "0.875rem", None),                                  # 14px: buttons, meta
    ("Medium",   "medium",   "1rem",     None),                                  # 16px: body
    ("Large",    "large",    "1.125rem", fluid("1.0625rem", "1.125rem")),        # 18px
    ("X Large",  "x-large",  "1.375rem", fluid("1.25rem", "1.375rem")),          # 22px: footer titles
    ("Heading",  "heading",  "1.625rem", fluid("1.25rem", "1.625rem")),          # 26px: card and work titles
    ("Display",  "display",  "2.875rem", fluid("1.625rem", "2.875rem")),         # 46px: section titles
    ("Colossal", "colossal", "3.125rem", fluid("1.875rem", "3.125rem")),         # 50px: opening statement, figures
]

# The design's sections are generous: 150px around the works, 200px above the
# services band, the product story and the figures, 85px under every section
# title. 80 and 90 are those two, reaching full size at 1440px and falling to
# the template's own 60px on a phone.
SPACING = [
    ("20", "0.5rem"),
    ("30", "1rem"),
    ("40", "1.5rem"),
    ("50", "clamp(2rem, 4vw, 2.5rem)"),
    ("60", "clamp(2.5rem, 6vw, 4rem)"),
    ("70", "clamp(3rem, 6vw, 5.3125rem)"),
    ("80", "clamp(3.75rem, 10.5vw, 9.375rem)"),
    ("90", "clamp(3.75rem, 14vw, 12.5rem)"),
]

# Every foreground/background pair the design puts together as body-size text.
CONTRAST_CHECKS = [
    ("contrast", "base"), ("contrast", "surface"),
    ("muted", "base"), ("muted", "surface"),
    ("primary", "base"), ("primary", "surface"),
    ("primary-deep", "base"),
    ("overlay", "dark"),
    # The footer and the services band set body text on the black ground.
    ("on-dark", "dark"),
    # Button labels resting and hovered, and the "Visit our work" band, which
    # sets the same pair full width.
    ("on-primary", "primary"),
    ("on-primary", "primary-deep"),
]

# Large text only (the 50px figures in the Quick Fact band): WCAG's 3:1.
LARGE_CHECKS = [
    ("secondary", "base"), ("tertiary", "base"), ("primary", "base"),
]


# ---------------------------------------------------------------------------
# Contrast
# ---------------------------------------------------------------------------
def _channel(value):
    value = value / 255
    return value / 12.92 if value <= 0.04045 else ((value + 0.055) / 1.055) ** 2.4


def luminance(hex_colour):
    r, g, b = (int(hex_colour[i:i + 2], 16) for i in (1, 3, 5))
    return 0.2126 * _channel(r) + 0.7152 * _channel(g) + 0.0722 * _channel(b)


def contrast_ratio(a, b):
    la, lb = luminance(a), luminance(b)
    lighter, darker = max(la, lb), min(la, lb)
    return (lighter + 0.05) / (darker + 0.05)


def button_text(colors):
    """The best label colour for a button on offer, chosen by measurement."""
    best, best_ratio = None, 0
    for slug in ("overlay", "base", "contrast", "dark"):
        ratio = min(contrast_ratio(colors[slug], colors["primary"]),
                    contrast_ratio(colors[slug], colors["primary-deep"]))
        if ratio > best_ratio:
            best, best_ratio = slug, ratio
    return best if best_ratio >= 4.5 else None


# ---------------------------------------------------------------------------
# theme.json
# ---------------------------------------------------------------------------
def od(*pairs):
    return collections.OrderedDict(pairs)


def var(slug):
    return "var(--wp--preset--color--%s)" % slug


def fs(slug):
    return "var(--wp--preset--font-size--%s)" % slug


def ff(slug):
    return "var(--wp--preset--font-family--%s)" % slug


def sp(slug):
    valid = {s for s, _ in SPACING}
    if slug not in valid:
        raise SystemExit("spacing %r is not on the scale %s" % (slug, sorted(valid)))
    return "var(--wp--preset--spacing--%s)" % slug


def palette(colors):
    return [od(("name", name), ("slug", slug), ("color", colors.get(slug, default)))
            for name, slug, default in PALETTE]


def font_families():
    out = []
    for key, (name, stack, prefix, weights) in FAMILIES.items():
        entry = od(("name", name), ("slug", key), ("fontFamily", stack))
        if weights:
            faces = []
            for weight in weights:
                for subset, ranges in (("latin", LATIN), ("latin-ext", LATIN_EXT)):
                    faces.append(od(
                        ("fontFamily", name), ("fontStyle", "normal"), ("fontWeight", weight),
                        ("fontDisplay", "swap"),
                        ("src", ["file:./assets/fonts/%s-%s-%s-normal.woff2" % (prefix, subset, weight)]),
                        ("unicodeRange", ranges),
                    ))
            entry["fontFace"] = faces
        out.append(entry)
    return out


def build_settings():
    return od(
        ("appearanceTools", True),
        ("useRootPaddingAwareAlignments", True),
        # The template is a Bootstrap 4 layout: a 1140px container with 15px
        # gutters, so text starts 165px in at 1440px. 1110px of content puts it
        # at exactly the same place.
        ("layout", od(("contentSize", "1110px"), ("wideSize", "1290px"))),
        ("color", od(("custom", True), ("defaultPalette", False), ("defaultGradients", False),
                     ("defaultDuotone", False),
                     ("palette", palette(COLOR_SETS["colors-1-azure"][1])))),
        ("typography", od(
            # Full size from 1200px, where the template stops scaling its type.
            ("fluid", od(("minViewportWidth", "390px"), ("maxViewportWidth", "1200px"))),
            ("customFontSize", True), ("defaultFontSizes", False),
            ("fontFamilies", font_families()),
            ("fontSizes", [od(("name", name), ("slug", slug), ("size", size)) if not f else
                           od(("name", name), ("slug", slug), ("size", size), ("fluid", f))
                           for name, slug, size, f in FONT_SIZES]),
        )),
        ("spacing", od(("units", ["px", "em", "rem", "vh", "vw", "%"]),
                       ("padding", True), ("margin", True), ("blockGap", True),
                       ("defaultSpacingSizes", False),
                       ("spacingSizes", [od(("name", name), ("slug", name), ("size", size))
                                         for name, size in SPACING]))),
        ("border", od(("color", True), ("radius", True), ("style", True), ("width", True))),
        ("shadow", od(("defaultPresets", False), ("presets", [
            # The blog cards' soft lift, as the template draws it.
            od(("name", "Soft"), ("slug", "soft"), ("shadow", "0 10px 20px rgba(160, 160, 160, 0.18)")),
            od(("name", "Lifted"), ("slug", "lifted"), ("shadow", "0 16px 36px rgba(0, 0, 0, 0.14)")),
        ]))),
    )


def build_styles():
    return od(
        # Body copy is the template's grey, not the heading colour: every
        # paragraph in the design is set lighter than the titles above it.
        ("color", od(("background", var("base")), ("text", var("muted")))),
        ("typography", od(("fontFamily", ff("poppins")), ("fontSize", fs("medium")),
                          ("fontWeight", "300"), ("lineHeight", "1.75"))),
        ("spacing", od(("blockGap", sp("40")),
                       ("padding", od(("left", sp("30")), ("right", sp("30")),
                                      ("top", "0px"), ("bottom", "0px"))))),
        ("elements", od(
            ("heading", od(("typography", od(("fontFamily", ff("poppins")), ("fontWeight", "600"),
                                             ("lineHeight", "1.26"))),
                           ("color", od(("text", var("contrast")))))),
            ("h1", od(("typography", od(("fontSize", fs("colossal")), ("fontWeight", "500"),
                                        ("lineHeight", "1.24"))))),
            ("h2", od(("typography", od(("fontSize", fs("display")))))),
            ("h3", od(("typography", od(("fontSize", fs("heading")), ("fontWeight", "500"))))),
            ("h4", od(("typography", od(("fontSize", fs("x-large")), ("fontWeight", "500"))))),
            ("h5", od(("typography", od(("fontSize", fs("large")), ("fontWeight", "500"))))),
            ("h6", od(("typography", od(("fontSize", fs("medium")), ("fontWeight", "500"))))),
            ("link", od(("color", od(("text", var("primary")))),
                        (":hover", od(("color", od(("text", var("primary-deep")))))))),
            ("button", od(
                # A filled pill, the shape of every button in the design. The
                # label is `on-primary`, which turns over with the fill in dark
                # mode where `overlay` deliberately never does.
                ("color", od(("background", var("primary")), ("text", var("on-primary")))),
                ("typography", od(("fontFamily", "inherit"), ("fontWeight", "400"),
                                  ("fontSize", fs("small")), ("lineHeight", "1.5"))),
                ("border", od(("radius", "30px"), ("width", "1px"), ("style", "solid"),
                              ("color", var("primary")))),
                ("spacing", od(("padding", od(("top", "1.0625rem"), ("bottom", "1.0625rem"),
                                              ("left", "3.25rem"), ("right", "3.25rem"))))),
                (":hover", od(("color", od(("background", var("primary-deep")), ("text", var("on-primary")))),
                              ("border", od(("color", var("primary-deep")))))),
                (":focus", od(("outline", od(("color", var("accent")), ("offset", "3px"),
                                             ("style", "solid"), ("width", "2px"))))),
            )),
            ("caption", od(("typography", od(("fontSize", fs("small")))),
                           ("color", od(("text", var("muted")))))),
        )),
        ("blocks", od(
            ("core/separator", od(("color", od(("text", var("divider")))))),
            ("core/site-title", od(("typography", od(("fontWeight", "600"), ("fontSize", fs("x-large")))))),
            ("core/navigation", od(("typography", od(("fontSize", fs("medium")), ("fontWeight", "400"))),
                                   ("color", od(("text", var("contrast")))))),
            ("core/post-title", od(("elements", od(("link", od(
                ("color", od(("text", var("contrast")))),
                (":hover", od(("color", od(("text", var("primary")))))),
            )))))),
            ("core/quote", od(("typography", od(("fontStyle", "italic"), ("fontWeight", "600"))),
                              ("color", od(("text", var("contrast")))))),
        )),
    )


def build_theme():
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3),
        ("settings", build_settings()),
        ("styles", build_styles()),
        ("customTemplates", [
            od(("name", "page-no-title"), ("title", "Page without title"), ("postTypes", ["page"])),
            od(("name", "page-with-sidebar"), ("title", "Page with sidebar"), ("postTypes", ["page"])),
            od(("name", "single-no-sidebar"), ("title", "Post without sidebar"), ("postTypes", ["post"])),
        ]),
        ("templateParts", [
            od(("name", "header"), ("title", "Header"), ("area", "header")),
            od(("name", "footer"), ("title", "Footer"), ("area", "footer")),
            od(("name", "sidebar"), ("title", "Sidebar"), ("area", "uncategorized")),
        ]),
    )


def build_color_variation(slug, name, colors):
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3), ("title", name),
        ("settings", od(("color", od(("palette", palette(colors)))))),
    )


def build_type_variation(slug, name, heading, body):
    return od(
        ("$schema", "https://schemas.wp.org/trunk/theme.json"),
        ("version", 3), ("title", name),
        ("styles", od(
            ("typography", od(("fontFamily", ff(body)))),
            ("elements", od(("heading", od(("typography", od(("fontFamily", ff(heading)))))))),
        )),
    )


def write(path, data):
    os.makedirs(os.path.dirname(path) or ".", exist_ok=True)
    with open(path, "w", encoding="utf-8") as handle:
        json.dump(data, handle, indent="\t", ensure_ascii=False)
        handle.write("\n")
    return path


def _mix_with_white(hex_colour, percent):
    """CSS `color-mix(in srgb, <colour> <percent>%, white)`, per channel."""
    channels = [int(hex_colour[i:i + 2], 16) for i in (1, 3, 5)]
    share = percent / 100
    return "#" + "".join("%02x" % round(c * share + 255 * (1 - share)) for c in channels)


def _dark_scheme():
    """What assets/css/scheme.css does to the palette, read from the file itself.

    Read rather than restated: the percentages live in the CSS, and a second
    copy here would drift from it. It also refuses any colour slug the palette
    does not define — a colour-mix on an undefined variable makes the whole
    declaration invalid, so the colour silently stops resolving.
    """
    import re

    css = open("assets/css/scheme.css", encoding="utf-8").read()
    defined = {slug for _, slug, _ in PALETTE}
    referenced = set(re.findall(r"var\(--wp--preset--color--([a-z0-9-]+)\)", css))
    unknown = sorted(referenced - defined)
    if unknown:
        raise SystemExit("scheme.css reads colour slugs the palette does not define: %s"
                         % ", ".join(unknown))

    def mix(name):
        m = re.search(r"--%s:\s*color-mix\(in srgb,\s*"
                      r"var\(--wp--preset--color--([a-z0-9-]+)\)\s*(\d+)%%,\s*white\)"
                      % re.escape(name), css)
        if not m:
            raise SystemExit("scheme.css: cannot read the dark-mode `%s` mix" % name)
        return m.group(1), int(m.group(2))

    def fixed(slug):
        m = re.search(r"--wp--preset--color--%s:\s*(#[0-9a-fA-F]{6})\s*;" % re.escape(slug), css)
        if not m:
            raise SystemExit("scheme.css: cannot read the dark-mode `%s`" % slug)
        return m.group(1).lower()

    return {
        "primary": mix("creative-agency-lift-primary"), "primary-deep": mix("creative-agency-lift-primary-deep"),
        "secondary": mix("creative-agency-lift-secondary"), "tertiary": mix("creative-agency-lift-tertiary"),
        "on-primary": fixed("on-primary"), "base": fixed("base"), "surface": fixed("surface"),
        "contrast": fixed("contrast"), "muted": fixed("muted"), "dark": fixed("dark"),
    }


def check_css_slugs():
    """Every palette variable any stylesheet reads must exist.

    A var() naming a slug the palette lacks does not error anywhere: the
    declaration is dropped and the element falls back, silently. Porting from
    another theme is exactly how that happens (its `success` or `flame` slug
    survives in a stylesheet that this palette never defines).
    """
    import glob
    import re

    defined = {slug for _, slug, _ in PALETTE}
    problems = []
    for path in ["style.css"] + sorted(glob.glob("assets/css/*.css")):
        css = open(path, encoding="utf-8").read()
        for slug in sorted(set(re.findall(r"--wp--preset--color--([a-z0-9-]+)", css)) - defined):
            problems.append("%s reads --wp--preset--color--%s, which the palette does not define" % (path, slug))
    return problems


def audit():
    problems = check_css_slugs()

    print("  light      label   resting  hover   large: secondary tertiary")
    for slug, (name, colors) in sorted(COLOR_SETS.items()):
        missing = [s for _, s, _ in PALETTE if s not in colors]
        if missing:
            problems.append("%s: palette is missing %s" % (name, ", ".join(missing)))
            continue
        for fg, bg in CONTRAST_CHECKS:
            ratio = contrast_ratio(colors[fg], colors[bg])
            if ratio < 4.5:
                problems.append("%s: %s on %s is %.2f" % (name, fg, bg, ratio))
        for fg, bg in LARGE_CHECKS:
            ratio = contrast_ratio(colors[fg], colors[bg])
            if ratio < 3.0:
                problems.append("%s: %s on %s is %.2f (large text needs 3:1)" % (name, fg, bg, ratio))
        resting = contrast_ratio(colors["on-primary"], colors["primary"])
        hover = contrast_ratio(colors["on-primary"], colors["primary-deep"])
        best = button_text(colors)
        if best is not None:
            best_worst = min(contrast_ratio(colors[best], colors[g]) for g in ("primary", "primary-deep"))
            if min(resting, hover) + 0.005 < best_worst:
                problems.append("%s: on-primary (%.2f) is a worse label than %s (%.2f)"
                                % (name, min(resting, hover), best, best_worst))
        print("  %-10s %-7s %6.2f  %6.2f          %6.2f   %6.2f" % (
            name, "on-prim", resting, hover,
            contrast_ratio(colors["secondary"], colors["base"]),
            contrast_ratio(colors["tertiary"], colors["base"])))

    dark = _dark_scheme()
    (psrc, pshare), (dsrc, dshare) = dark["primary"], dark["primary-deep"]
    (ssrc, sshare), (tsrc, tshare) = dark["secondary"], dark["tertiary"]
    print("\n  dark mode, as scheme.css applies it: primary = %s %d%%, primary-deep = %s %d%%, "
          "secondary = %s %d%%, tertiary = %s %d%% (each + white)"
          % (psrc, pshare, dsrc, dshare, ssrc, sshare, tsrc, tshare))
    print("  dark       text/base  text/surf  hover  label  label-hover  boundary  secondary  tertiary")
    for slug, (name, colors) in sorted(COLOR_SETS.items()):
        fill = _mix_with_white(colors[psrc], pshare)
        deep = _mix_with_white(colors[dsrc], dshare)
        sec = _mix_with_white(colors[ssrc], sshare)
        ter = _mix_with_white(colors[tsrc], tshare)
        measured = (
            ("primary text on base", contrast_ratio(fill, dark["base"]), 4.5),
            ("primary text on surface", contrast_ratio(fill, dark["surface"]), 4.5),
            ("hovered link on base", contrast_ratio(deep, dark["base"]), 4.5),
            ("button label on its fill", contrast_ratio(dark["on-primary"], fill), 4.5),
            ("button label on the hover fill", contrast_ratio(dark["on-primary"], deep), 4.5),
            # WCAG 1.4.11: a button has to be visible as a button, not merely
            # carry a readable label.
            ("button fill against the page",
             min(contrast_ratio(fill, dark["base"]), contrast_ratio(fill, dark["surface"])), 3.0),
            ("secondary figure on base", contrast_ratio(sec, dark["base"]), 3.0),
            ("tertiary figure on base", contrast_ratio(ter, dark["base"]), 3.0),
        )
        for label, value, need in measured:
            if value < need:
                problems.append("%s (dark): %s is %.2f, needs %.1f" % (name, label, value, need))
        print("  %-10s %9.2f  %9.2f  %5.2f  %5.2f  %11.2f  %8.2f  %9.2f  %8.2f"
              % ((name,) + tuple(v for _, v, _ in measured)))
    for fg, bg in (("contrast", "base"), ("contrast", "surface"), ("muted", "base"), ("muted", "surface")):
        ratio = contrast_ratio(dark[fg], dark[bg])
        if ratio < 4.5:
            problems.append("dark: %s on %s is %.2f" % (fg, bg, ratio))
    # The black band and footer stay black; their text is `overlay`/`on-dark`,
    # which scheme.css never redefines, so those pairs hold in both schemes.
    for slug, (name, colors) in sorted(COLOR_SETS.items()):
        for fg in ("overlay", "on-dark"):
            ratio = contrast_ratio(colors[fg], dark["dark"])
            if ratio < 4.5:
                problems.append("%s (dark): %s on dark is %.2f" % (name, fg, ratio))

    print("\n  for the record: the template's #00a7ff is %.2f:1 on white, #fd8e5e %.2f:1, #919191 %.2f:1,"
          % (contrast_ratio("#00a7ff", "#ffffff"), contrast_ratio("#fd8e5e", "#ffffff"),
             contrast_ratio("#919191", "#ffffff")))
    print("  and #ffcb00 %.2f:1 — the first and last ship as decoration, the other two were deepened."
          % contrast_ratio("#ffcb00", "#ffffff"))
    if problems:
        raise SystemExit("\nContrast failures:\n  " + "\n  ".join(problems))


def main():
    audit()
    written = [write("theme.json", build_theme())]
    for slug, (name, colors) in sorted(COLOR_SETS.items()):
        written.append(write("styles/colors/%s.json" % slug, build_color_variation(slug, name, colors)))
    for slug, (name, heading, body) in sorted(TYPE_SETS.items()):
        written.append(write("styles/typography/%s.json" % slug, build_type_variation(slug, name, heading, body)))
    print("\n  %d files written" % len(written))
    for path in written:
        print("    " + path)


if __name__ == "__main__":
    main()
