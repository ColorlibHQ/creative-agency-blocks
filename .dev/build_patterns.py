#!/usr/bin/env python3
"""Generate Creative Agency's patterns.

Run from the theme root:

    python3 .dev/build_patterns.py
    node .dev/normalize-blocks.mjs      # then let the editor re-serialise them
    node .dev/validate-blocks.mjs       # and refuse anything it calls invalid

Every pattern file is committed as generated. Edit this file, never
patterns/*.php.

The design is Colorlib's "Creative Agency" template (the one the WordPress
demo at colorlibhub.com/creative-agency/ was built from): a white page, one
large statement per screen, pale giant words behind the section titles, a
black services band, a strip of studio photographs and a blue band before a
black footer. The story is one studio's: a design and development studio in
Oakland, California, with four projects that each get their own page.
"""

import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from patternlib import (  # noqa: E402
    button, buttons, column, columns, cover, group, heading, image,
    paragraph, shortcode, sp,
)

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PATTERNS = os.path.join(ROOT, "patterns")
WRITTEN = []

P = "creative-agency"
SECTIONS = ["creative-agency-sections"]
PAGES = ["creative-agency-pages"]


def c(name):
    """A theme class name."""
    return "%s-%s" % (P, name)


def write(slug, title, content, categories=None, keywords=None,
          description=None, inserter=True, block_types=None):
    header = ["Title: " + title, "Slug: %s/%s" % (P, slug)]
    if categories:
        header.append("Categories: " + ", ".join(categories))
    if keywords:
        header.append("Keywords: " + ", ".join(keywords))
    if block_types:
        header.append("Block Types: " + ", ".join(block_types))
    if description:
        header.append("Description: " + description)
    if not inserter:
        header.append("Inserter: no")

    body = (
        "<?php\n/**\n * " + "\n * ".join(header) + "\n *\n * @package Creative_Agency\n */\n\n"
        "defined( 'ABSPATH' ) || exit;\n?>\n" + content.strip() + "\n"
    )
    with open(os.path.join(PATTERNS, slug + ".php"), "w") as fh:
        fh.write(body)
    WRITTEN.append(slug)


def link(path):
    """A link to one of the starter pages.

    Written through home_url() so it is right on a site installed in a
    subdirectory. A pattern's PHP runs when the pattern is registered, so a
    starter page built from it stores the resolved address, and
    inc/front-page-setup.php then swaps each one for the real permalink of the
    page it created.
    """
    return "<?php echo esc_url( home_url( '/%s' ) ); ?>" % path


def icon_class(name):
    """The class that draws a Tabler icon, carried by the block itself.

    style.css draws it with a mask filled from the text colour, so it follows
    the palette and dark mode, shows in the editor (an empty span inside a
    paragraph does not), and changing it is an edit to Advanced → Additional
    CSS class(es). Every name used here needs a `.creative-agency-icon--<name>`
    rule; .dev/dead-selectors.py checks that it has one.
    """
    return c("icon--%s" % name)


def flex_row(inner, justify=None, gap=None, wrap="wrap", vertical="center", extra_class=None):
    """A horizontal group. normalize-blocks.mjs rewrites it into core's own shape."""
    layout = {"type": "flex", "flexWrap": wrap}
    if justify:
        layout["justifyContent"] = justify
    if vertical:
        layout["verticalAlignment"] = vertical
    data = {}
    if extra_class:
        data["className"] = extra_class
    if gap:
        data["style"] = {"spacing": {"blockGap": sp(gap)}}
    data["layout"] = layout
    cls = "wp-block-group" + (" " + extra_class if extra_class else "")
    return '<!-- wp:group %s -->\n<div class="%s">\n%s\n</div>\n<!-- /wp:group -->' % (
        json.dumps(data, separators=(",", ":")), cls, inner
    )


def watermark(text, centred=False):
    """The giant pale word behind a section title ("Projects", "Services").

    A paragraph with a block style, so an owner can rewrite it or add one to a
    section of their own. It is decoration: functions.php hides it from screen
    readers, and style.css drops it below 992px, as the design does.
    """
    return paragraph(text, style=c("watermark"),
                     extra_class=c("watermark--centre") if centred else None)


def underline_button(text, url):
    """A text link carried on a yellow bar: "Browse our work", "Say hi"."""
    return button(text, url, style=c("underline"))


def statement(html, level=1, caps=False):
    """The opening statement every page begins with: 50px, weight 500."""
    return heading(html, level=level, extra_class=c("statement"),
                   transform="uppercase" if caps else None)


def hero_band(inner, extra_class=None, anchor=None):
    """The white opening band with its two floating shapes.

    The shapes (a lilac circle and a mint triangle) are drawn by style.css on
    this class, from palette colours, so they follow every palette and dark
    mode without an image file.
    """
    return group(inner, align="full", padding={"top": "80", "bottom": "80"}, gap="50",
                 layout="constrained", extra_class=(c("hero") + (" " + extra_class if extra_class else "")),
                 anchor=anchor)


# ---------------------------------------------------------------------------
# Content: one studio, four projects
# ---------------------------------------------------------------------------
PROJECTS = [
    {
        "slug": "nookdesk", "title": "Nookdesk booking app", "short": "Nookdesk",
        "card": "work-nookdesk", "photo": "work-nookdesk",
        "alt": "A white desk with a laptop and a tablet set up in the bright corner of an office",
        "client": "Nookdesk", "service": "Research and iOS app", "year": "2025",
        "copy": [
            "Nookdesk runs flexible workspaces in four Bay Area buildings. Members booked desks "
            "through a web form and a shared spreadsheet, and on a busy Monday half of them arrived "
            "to find their desk already taken.",
            "We interviewed members and the front-desk team, mapped a week of bookings, and designed an "
            "iOS app around the one question people ask on the way in: where can I sit today? Booking "
            "takes two taps, and the floor plan shows who is already in.",
        ],
        "points": [
            "Twenty-two member interviews and a week at the front desk.",
            "A live floor plan that replaced the spreadsheet on day one.",
            "No-shows down by a third in the first quarter.",
        ],
    },
    {
        "slug": "whisk", "title": "Whisk design system", "short": "Whisk",
        "card": "work-whisk", "photo": "work-whisk",
        "alt": "A hand holding up a rainbow-coloured whisk dipped in meringue",
        "client": "Whisk", "service": "Design system", "year": "2025",
        "copy": [
            "Whisk is a recipe app with a small team and a large library: six hundred recipes, three "
            "platforms, and a component for every one-off screen anyone had ever needed.",
            "We audited every screen, cut the component library from 214 parts to 48, and wrote the "
            "tokens and documentation that let designers and developers speak the same language. New "
            "screens now ship in days rather than sprints.",
        ],
        "points": [
            "One token set for colour, type and spacing across iOS, Android and web.",
            "Forty-eight documented components, each with usage notes and code.",
            "A contribution process the in-house team now runs without us.",
        ],
    },
    {
        "slug": "harrow-and-pine", "title": "Harrow &amp; Pine packaging", "short": "Harrow &amp; Pine",
        "card": "work-harrow", "photo": "work-harrow",
        "alt": "An amber glass dropper bottle with a blank white label, standing on a stone block",
        "client": "Harrow &amp; Pine", "service": "Packaging and labels", "year": "2024",
        "copy": [
            "Harrow &amp; Pine is a two-person apothecary in Mendocino that distils its own pine and cedar "
            "oils. Its first face oil needed to look as considered as the recipe, and to hold its own on "
            "a shelf crowded with louder bottles.",
            "We kept the amber glass and the black dropper, drew a quiet label system from the founders' "
            "hand-written batch notes, chose a paper that survives a steamy bathroom, and designed the "
            "carton, the shipper and the refill pouch to match.",
        ],
        "points": [
            "Label, carton and refill pouch, printed by a local press.",
            "Artwork built for two bottle sizes and three seasonal oils.",
            "A shop-counter display cut from the same board as the carton.",
        ],
    },
    {
        "slug": "arcade-club", "title": "Arcade Club app", "short": "Arcade Club",
        "card": "work-arcade-card", "photo": "work-arcade",
        "alt": "A phone with a neon lock screen lying on a white games console",
        "client": "Arcade Club", "service": "Mobile app", "year": "2026",
        "copy": [
            "Arcade Club is a gaming lounge in downtown Oakland with forty consoles, a tournament every "
            "Friday, and a queue that used to live on a whiteboard by the door.",
            "We designed and built a companion app that holds your place in the queue, books a console "
            "for a group, and turns the lock screen into a live tournament bracket. It launched on iOS "
            "and Android in the same week.",
        ],
        "points": [
            "A live queue that tells you ten minutes before your turn.",
            "Group bookings and tournament sign-up in one flow.",
            "A React Native codebase handed over to the club's own developer.",
        ],
    },
]

SERVICES = [
    ("user-search", "UX Research", "primary",
     "Interviews, usability tests and analytics, so the brief is built on what people actually do."),
    ("palette", "UI Design", "tertiary",
     "Interfaces that feel obvious the first time, from the first wireframe to the last hover state."),
    ("code", "Development", "secondary",
     "Native iOS and Android, React on the web, and a handover your own team can maintain."),
    ("vector-bezier", "Brand Identity", "primary",
     "Names, marks and type systems that hold together at sixteen pixels and on a billboard."),
    ("package", "Packaging", "tertiary",
     "Labels and boxes designed around the shelf, the printer's limits and the unboxing."),
    ("components", "Design Systems", "secondary",
     "Tokens, components and documentation that let a product grow without drifting."),
]

CLIENTS = [
    ("nookdesk", "Nookdesk"), ("whisk", "Whisk"), ("harrow", "Harrow &amp; Pine"),
    ("arcade", "Arcade Club"), ("tidepool", "Tidepool"), ("fieldnote", "Fieldnote"),
]

STUDIO = [
    ("studio-1", "Two designers planning a project on a wall of sticky notes"),
    ("studio-2", "A designer in glasses smiling at the studio table, a colleague and a laptop beside him"),
    ("studio-3", "The team at a long white table, working on their laptops"),
    ("studio-4", "A tidy desk with a laptop and a tablet side by side under a lamp"),
    ("studio-5", "A meeting around a monitor on a wall covered in sticky notes"),
]

FACTS = [("214", "Products shipped", "secondary"), ("96", "Clients since 2014", "primary"),
         ("18", "Design awards", "tertiary")]

ADDRESS = ("410 Broadway, Oakland", "California, CA 94607")
PHONE = ("+1 (510) 555-0142", "Monday to Friday, 9am to 6pm")
EMAIL = ("studio@yourdomain.com", "Send us your brief any time")
# "Making Material Design" (Google Design, 2015): designers sketching, cutting paper and
# prototyping. Public and embeddable; not another studio's sales reel. Replace with your own.
VIDEO = "https://www.youtube.com/watch?v=rrT6v5sOwJg"


# ---------------------------------------------------------------------------
# Parts
# ---------------------------------------------------------------------------
def build_header():
    # The brand tile: the site's initial in a blue square, flush with the top
    # left corner, as the design draws its logo. style.css shows only the first
    # letter of the site title; the full name is still there for screen readers
    # and search engines. A site with a logo shows the logo in the tile instead.
    brand = group('<!-- wp:site-logo {"width":100} /-->\n<!-- wp:site-title {"level":0} /-->',
                  layout="flex", extra_class=c("brand"))
    nav = ('<!-- wp:navigation {"overlayMenu":"mobile","className":"%s","layout":'
           '{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} /-->' % c("nav"))
    # The switch needs text inside it: an empty core/button renders nothing at
    # all. The label is for screen readers; inc/scheme.php adds the pressed state.
    toggle = button('<span class="screen-reader-text">Switch between light and dark mode</span>', "#",
                    extra_class=c("scheme-toggle"))
    actions = buttons([underline_button("Say hi", link("contact/")), toggle], align="right", nowrap=True)
    header = group("\n".join([brand, nav, actions]), align="full", layout="flex",
                   justify="space-between", wrap="nowrap", vertical="center", extra_class=c("header"))
    write("header", "Header", header, keywords=["header", "navigation"],
          description="The site's initial in a blue tile, the navigation centred, and a Say hi link. "
                      "It turns black and follows the page once you scroll.",
          block_types=["core/template-part/header"])


def footer_list(items):
    lis = "".join('<li><a href="%s">%s</a></li>' % (u, t) for t, u in items)
    return ('<!-- wp:list {"className":"is-style-%s"} -->\n'
            '<ul class="wp-block-list is-style-%s">%s</ul>\n<!-- /wp:list -->' % (c("links"), c("links"), lis))


def build_footer():
    follow = column("\n".join([
        heading("Follow us", level=2, color="overlay", extra_class=c("footer-title")),
        footer_list([("Instagram", "https://www.instagram.com/"), ("Dribbble", "https://dribbble.com/"),
                     ("Behance", "https://www.behance.net/"), ("LinkedIn", "https://www.linkedin.com/"),
                     ("YouTube", "https://www.youtube.com/")]),
    ]))
    links = column("\n".join([
        heading("Links", level=2, color="overlay", extra_class=c("footer-title")),
        footer_list([("Services", link("services/")), ("Work", link("work/")), ("About", link("about/")),
                     ("Blog", link("blog/")), ("Contact", link("contact/"))]),
    ]))
    address = column("\n".join([
        heading("Address", level=2, color="overlay", extra_class=c("footer-title")),
        paragraph("%s, %s<br><a href=\"mailto:%s\">%s</a><br><a href=\"tel:+15105550142\">%s</a>"
                  % (ADDRESS[0], ADDRESS[1].replace("California, ", ""), EMAIL[0], EMAIL[0], PHONE[0]),
                  color="on-dark", extra_class=c("footer-address")),
    ]))
    top = group(columns([follow, links, address], gap="40", extra_class=c("footer-columns")),
                layout="constrained", padding={"top": "80", "bottom": "80"})
    legal = group(
        paragraph('Copyright © <?php echo esc_html( gmdate( \'Y\' ) ); ?> All rights reserved · '
                  'Theme by <a href="https://colorlib.com/" rel="nofollow">Colorlib</a>',
                  align="center", color="on-dark", size="medium", extra_class=c("copyright")),
        layout="constrained", padding={"top": "40", "bottom": "40"}, extra_class=c("footer-legal"))
    # Padding stated as zero: a group with a background and no padding of its
    # own gets core's 1.25em, which put 20px of black above the footer.
    body = group(top + "\n" + legal, align="full", background="dark", text="on-dark",
                 layout="constrained", gap="0", extra_class=c("footer"), padding={"top": "0", "bottom": "0"})
    write("footer", "Footer", body, keywords=["footer"],
          description="Three columns on black — social links, pages and the studio's address — "
                      "with a copyright line beneath.",
          block_types=["core/template-part/footer"])


def panel(title, inner):
    """A sidebar widget: the lavender panel with its hairline under the title."""
    return group("\n".join([heading(title, level=2, extra_class=c("widget-title")), inner]),
                 layout="constrained", gap="50", padding={"top": "40", "bottom": "40", "left": "40", "right": "40"},
                 background="surface", style=c("panel"))


def build_sidebar():
    inner = "\n".join([
        group('<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search keyword",'
              '"buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /-->',
              layout="constrained", padding={"top": "40", "bottom": "40", "left": "40", "right": "40"},
              background="surface", style=c("panel")),
        panel("Categories", '<!-- wp:categories {"showPostCounts":true} /-->'),
        panel("Recent posts", '<!-- wp:latest-posts {"postsToShow":4,"displayPostDate":true,'
                              '"displayFeaturedImage":true,"featuredImageAlign":"left",'
                              '"featuredImageSizeSlug":"thumbnail","featuredImageSizeWidth":80,'
                              '"featuredImageSizeHeight":80,"className":"%s"} /-->' % c("recent")),
        panel("Tags", '<!-- wp:tag-cloud {"className":"is-style-outline"} /-->'),
        panel("Start a project", "\n".join([
            paragraph("Tell us what you are building and we will reply within two working days."),
            buttons([underline_button("Say hi", link("contact/"))]),
        ])),
    ])
    write("sidebar", "Sidebar", group(inner, layout="constrained", gap="40", extra_class=c("sidebar")),
          keywords=["sidebar"], inserter=False,
          description="Search, categories, recent posts, tags and a call to start a project.")


# ---------------------------------------------------------------------------
# Sections
# ---------------------------------------------------------------------------
def build_hero():
    inner = "\n".join([
        statement('We are a <span style="text-decoration: underline;">design and development</span>'
                  ' <br>studio based in California'),
        buttons([underline_button("Browse our work", link("work/"))]),
    ])
    write("hero", "Opening statement", hero_band(inner),
          categories=SECTIONS, keywords=["hero", "intro", "statement"],
          description="The page's opening line in large type, with a link beneath it and two floating shapes.")


def build_page_headings():
    headings = {
        "heading-about": ("About: opening statement",
                          'A small studio making <span style="text-decoration: underline;">digital products</span>'
                          ' <br>in Oakland since 2014', False),
        "heading-work": ("Work: opening statement",
                         "Take a look at what we have built <br>for businesses and brands", False),
        "heading-services": ("Services: opening statement",
                             "We are a full-service design studio, <br>we build digital products", False),
        "heading-contact": ("Contact: opening statement", "Contact", True),
    }
    for slug, (title, text, caps) in headings.items():
        write(slug, title, hero_band(statement(text, caps=caps)), categories=SECTIONS,
              keywords=["heading", "intro", "statement"],
              description="An opening statement for an inner page, with the two floating shapes.")


def build_video():
    # A photograph of the studio with a round play button. The button opens the
    # film in a popup (assets/js/interactions.js); without JavaScript it is an
    # ordinary link to the video.
    play = buttons([button('<span class="screen-reader-text">Play the video</span>', VIDEO,
                           extra_class=c("video") + " " + c("play"))], align="center")
    write("video", "Studio film",
          cover(play, "studio-meeting", overlay="dark", dim=0, align="full", extra_class=c("film")),
          categories=SECTIONS, keywords=["video", "film", "photo"],
          description="A full-width photograph of the studio with a play button that opens a video.")


def work_item(project):
    url = link("work/%s/" % project["slug"])
    media = group("\n".join([
        image(project["card"], project["alt"], ratio="46/47"),
        buttons([button("View details", url, extra_class=c("work__more"))], align="center"),
    ]), layout="default", extra_class=c("work__media"))
    title = heading('<a href="%s">%s</a>' % (url, project["title"]), level=3,
                    style=c("marked"), extra_class=c("work__title"))
    return group(media + "\n" + title, layout="default", gap="0", extra_class=c("work"))


def works_grid():
    left = [PROJECTS[0], PROJECTS[1]]
    right = [PROJECTS[2], PROJECTS[3]]
    return columns([
        column(group("\n".join(work_item(p) for p in left), layout="default", gap="70")),
        column(group("\n".join(work_item(p) for p in right), layout="default", gap="70")),
    ], extra_class=c("works__grid"))


def build_works(slug="works", with_title=True):
    parts = [watermark("Projects")]
    if with_title:
        parts.append(heading("Our works", level=2))
    parts += [works_grid(), buttons([button("More projects", link("work/"), style="outline")], align="center")]
    # On the Work page the grid follows the opening band directly, so it starts
    # lower and clears the watermark, as the design's does.
    section = group("\n".join(parts), align="full", padding={"top": "80" if with_title else "90", "bottom": "80"}, gap="70",
                    layout="constrained", extra_class=c("works"), anchor="work" if with_title else None)
    if with_title:
        write(slug, "Works: four projects", section, categories=SECTIONS,
              keywords=["portfolio", "work", "projects", "case studies"],
              description="Four projects in a staggered two-column grid, each linking to its own page.")
    else:
        write(slug, "Works: project grid", section, inserter=False,
              description="The project grid without its title, for the Work page.")


def service_card(icon, title, colour, blurb, on_dark):
    inner = "\n".join([
        paragraph("", extra_class="%s %s %s" % (c("service__icon"), c("service__icon--" + colour), icon_class(icon)),
                  placeholder=" "),
        heading(title, level=3, align="center", color="overlay" if on_dark else None),
        paragraph(blurb, align="center", color="overlay" if on_dark else None),
    ])
    return column(group(inner, layout="constrained", gap="30", style=c("card"),
                        padding={"top": "60", "bottom": "60", "left": "50", "right": "50"}))


def clients_row():
    logos = "\n".join(image("clients/" + s, "%s logo" % name, ext="svg", width="72px", size="full")
                      for s, name in CLIENTS)
    return flex_row(logos, justify="space-between", gap="40", extra_class=c("clients"))


def build_services_dark():
    cards = columns([service_card(i, t, col, b, True) for i, t, col, b in SERVICES[:3]], gap="40")
    inner = "\n".join([
        watermark("Services"),
        heading("We’re a full-service design <br>studio, building digital products <br>and brands",
                level=2, color="overlay"),
        cards,
        clients_row(),
    ])
    write("services-dark", "Services: black band with client logos",
          group(inner, align="full", background="dark", text="overlay",
                padding={"top": "90", "bottom": "80"}, gap="70", layout="constrained",
                extra_class=c("services"), anchor="services"),
          categories=SECTIONS, keywords=["services", "cards", "clients", "logos"],
          description="Three services on the black band, with a row of client logos beneath.")


def build_services_grid():
    rows = [columns([service_card(i, t, col, b, False) for i, t, col, b in SERVICES[k:k + 3]], gap="40")
            for k in (0, 3)]
    write("services-grid", "Services: six cards",
          group("\n".join(rows), align="full", padding={"top": "40", "bottom": "60"}, gap="40",
                layout="constrained", extra_class=c("services-grid")),
          categories=SECTIONS, keywords=["services", "cards"],
          description="Six services in two rows of outlined cards, each with a coloured icon.")


def build_clients():
    write("clients", "Client logos",
          group(clients_row(), align="full", padding={"top": "80", "bottom": "0"}, layout="constrained"),
          categories=SECTIONS, keywords=["clients", "logos", "brands"],
          description="A row of six client logos in grey.")


def build_story():
    text = "\n".join([
        heading("We help you build your product and brand, big or small", level=2),
        paragraph("From the first workshop to the app store listing, one team stays with the work. "
                  "Start-ups get a studio that has shipped before; established brands get people who "
                  "still care about the last pixel."),
        buttons([underline_button("Meet the studio", link("about/"))]),
    ])
    inner = columns([
        column(image("product-phone", "A hand holding a phone with a travel app open, over a laptop keyboard"),
               width="50%"),
        column(group(text, layout="constrained", gap="40"), width="41.66%", vertical="center"),
    ], gap="40", vertical="center", extra_class=c("split"))
    write("story", "Story: photograph and text",
          group(inner, align="full", padding={"top": "90", "bottom": "0"}, layout="constrained"),
          categories=SECTIONS, keywords=["about", "story", "image", "text"],
          description="A photograph beside a heading, a paragraph and a link.")


def build_facts():
    cols = []
    for number, label, colour in FACTS:
        cols.append(column(group("\n".join([
            # assets/js/interactions.js counts this figure up from zero.
            heading(number, level=3, align="center", color=colour, extra_class=c("count")),
            paragraph(label, align="center", color="contrast"),
        ]), layout="constrained", gap="20")))
    inner = watermark("Quick Fact", centred=True) + "\n" + columns(cols, gap="40")
    write("facts", "Quick facts",
          group(inner, align="full", padding={"top": "90", "bottom": "90"}, layout="constrained",
                extra_class=c("facts")),
          categories=SECTIONS, keywords=["statistics", "counters", "numbers", "facts"],
          description="Three figures that count up as they come into view, over a giant pale title.")


def build_strip():
    cols = [column(image(s, alt, ratio="19/20", lightbox=True)) for s, alt in STUDIO]
    write("studio-strip", "Studio photographs",
          columns(cols, align="full", gap="0", extra_class=c("strip")),
          categories=SECTIONS, keywords=["gallery", "photos", "instagram", "studio"],
          description="Five photographs edge to edge; each enlarges on click.")


def build_band():
    inner = paragraph('<a href="%s">Visit our work</a>' % link("work/"), align="center",
                      size="heading", extra_class=c("band__link"))
    write("cta-band", "Call to action band",
          group(inner, align="full", background="primary", text="on-primary",
                padding={"top": "50", "bottom": "50"}, layout="constrained", extra_class=c("band")),
          categories=SECTIONS, keywords=["cta", "call to action", "band"],
          description="A full-width band with one large link.")


def contact_detail(icon, strong, soft):
    return paragraph("<strong>%s</strong><br>%s" % (strong, soft),
                     extra_class="%s %s" % (c("detail"), icon_class(icon)))


def build_contact():
    osm = ("https://www.openstreetmap.org/export/embed.html?bbox=-122.2830%2C37.7930%2C-122.2680%2C37.8020"
           "&amp;layer=mapnik&amp;marker=37.7975%2C-122.2755")
    map_block = (
        '<!-- wp:html -->\n'
        '<iframe class="%s" title="Map showing where the studio is" loading="lazy" src="%s" '
        'style="width:100%%;height:480px;border:0"></iframe>\n'
        '<!-- /wp:html -->' % (c("map"), osm)
    )
    form = group("\n".join([
        heading("Get in touch", level=2, extra_class=c("contact-title")),
        shortcode("[creative_agency_contact_form]"),
    ]), layout="constrained", gap="30")
    details = group("\n".join([
        contact_detail("map-pin", ADDRESS[0], ADDRESS[1]),
        contact_detail("device-mobile", PHONE[0], PHONE[1]),
        contact_detail("mail", EMAIL[0], EMAIL[1]),
    ]), layout="constrained", gap="50", extra_class=c("contact-details"))
    inner = map_block + "\n" + columns([column(form, width="66.66%"),
                                        column(details, width="33.33%")], gap="60")
    write("contact", "Contact: map, form and details",
          group(inner, align="full", padding={"top": "80", "bottom": "90"}, gap="60",
                layout="constrained", anchor="contact"),
          categories=SECTIONS, keywords=["contact", "form", "map"],
          description="A map, then the contact form beside the studio's address, phone and email.")


def project_body(project, prev_p, next_p):
    meta = columns([
        column("\n".join([
            paragraph("Client", color="secondary", extra_class=c("meta-label")),
            paragraph(project["client"], color="contrast"),
        ])),
        column("\n".join([
            paragraph("Service", color="secondary", extra_class=c("meta-label")),
            paragraph(project["service"], color="contrast"),
        ])),
        column("\n".join([
            paragraph("Year", color="secondary", extra_class=c("meta-label")),
            paragraph(project["year"], color="contrast"),
        ])),
        column('<!-- wp:social-links {"size":"has-normal-icon-size","className":"%s","layout":'
               '{"type":"flex","justifyContent":"right"}} -->\n'
               '<ul class="wp-block-social-links has-normal-icon-size %s">'
               '<!-- wp:social-link {"url":"https://www.facebook.com/","service":"facebook"} /-->'
               '<!-- wp:social-link {"url":"https://www.pinterest.com/","service":"pinterest"} /-->'
               '<!-- wp:social-link {"url":"https://x.com/","service":"x"} /-->'
               '</ul>\n<!-- /wp:social-links -->' % (c("share"), c("share")), width="40%"),
    ], gap="40", extra_class=c("project-meta"))
    points = "".join("<li>%s</li>" % p for p in project["points"])
    details = columns([
        column("\n".join([
            heading("Project details", level=2, extra_class=c("project-title")),
            "\n".join(paragraph(p) for p in project["copy"]),
            '<!-- wp:list {"className":"is-style-%s"} -->\n<ul class="wp-block-list is-style-%s">%s</ul>\n'
            '<!-- /wp:list -->' % (c("dots"), c("dots"), points),
        ]), width="58.33%"),
        column(buttons([button("Live view", link("contact/"), style="outline")], align="right"),
               width="41.66%"),
    ], gap="40", extra_class=c("project-details"))

    def nav_link(p, label, side):
        return column("\n".join([
            paragraph(label, align=side, extra_class=c("project-nav__label") + " " + c("project-nav__label--" + side)),
            heading('<a href="%s">%s</a>' % (link("work/%s/" % p["slug"]), p["title"]), level=3,
                    align=side, extra_class=c("project-nav__title")),
        ]))
    nav = columns([nav_link(prev_p, "Previous", "left"), nav_link(next_p, "Next", "right")],
                  gap="40", extra_class=c("project-nav"))
    inner = "\n".join([
        image(project["photo"], project["alt"], ratio="16/9"),
        meta,
        '<!-- wp:separator {"className":"is-style-wide"} -->\n'
        '<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>\n<!-- /wp:separator -->',
        details,
        nav,
    ])
    return group(inner, align="full", padding={"top": "20", "bottom": "0"}, gap="60",
                 layout="constrained", extra_class=c("project"))


def build_projects():
    n = len(PROJECTS)
    for i, project in enumerate(PROJECTS):
        prev_p, next_p = PROJECTS[(i - 1) % n], PROJECTS[(i + 1) % n]
        write("project-%s" % project["slug"], "Project: %s" % project["short"],
              project_body(project, prev_p, next_p), inserter=(i == 0),
              categories=SECTIONS if i == 0 else None,
              keywords=["project", "case study", "portfolio"],
              description="A project page: large photograph, client, service and year, the story, "
                          "and links to the previous and next projects.")
        write("heading-project-%s" % project["slug"], "Project heading: %s" % project["short"],
              hero_band(statement(project["title"]), extra_class=c("hero--tight")), inserter=False,
              description="The opening statement of a project page.")


def post_card(level=2):
    """A blog card as the design draws it: photograph, a date badge overlapping
    its lower edge, then the title, excerpt and category on a white panel with
    a soft shadow."""
    badge = group('<!-- wp:post-date {"format":"j","className":"%s"} /-->\n'
                  '<!-- wp:post-date {"format":"M","className":"%s"} /-->' % (c("date-badge__day"), c("date-badge__month")),
                  layout="default", gap="0", extra_class=c("date-badge"))
    media = group('<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"2/1"} /-->\n' + badge,
                  layout="default", gap="0", extra_class=c("post-media"))
    meta = flex_row('<!-- wp:post-terms {"term":"category","className":"%s"} /-->\n'
                    '<!-- wp:post-author-name {"className":"%s"} /-->' % (c("meta-terms"), c("meta-author")),
                    gap="30", extra_class=c("post-meta"))
    text = group("\n".join([
        '<!-- wp:post-title {"isLink":true,"level":%d,"className":"%s"} /-->' % (level, c("card-title")),
        '<!-- wp:post-excerpt {"excerptLength":24} /-->',
        meta,
    ]), layout="constrained", gap="30", extra_class=c("post-text"))
    return group(media + "\n" + text, layout="default", gap="0", shadow="soft", extra_class=c("post-card"))


def build_blog_latest():
    query = (
        '<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post",'
        '"order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->\n'
        '<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"%s"}},'
        '"layout":{"type":"grid","columnCount":3}} -->\n%s\n<!-- /wp:post-template --></div>\n'
        '<!-- /wp:query -->' % (sp("40"), post_card(level=3))
    )
    inner = "\n".join([watermark("Journal"), heading("From the journal", level=2), query])
    write("blog-latest", "Latest posts",
          group(inner, align="full", padding={"top": "80", "bottom": "90"}, gap="70", layout="constrained",
                extra_class=c("journal")),
          categories=SECTIONS, keywords=["blog", "posts", "news", "journal"],
          description="The three most recent posts as cards with a date badge.")


# ---------------------------------------------------------------------------
# Hidden patterns: the pieces templates are built from.
# ---------------------------------------------------------------------------
def build_hidden():
    write("hidden-page-banner", "Page banner",
          hero_band('<!-- wp:post-title {"level":1,"className":"%s"} /-->' % c("statement")),
          inserter=False, description="The opening band a page title sits in.")

    write("hidden-single-banner", "Post banner",
          hero_band("\n".join([
              '<!-- wp:post-terms {"term":"category","className":"%s"} /-->' % c("eyebrow"),
              '<!-- wp:post-title {"level":1,"className":"%s"} /-->' % c("statement"),
          ])), inserter=False, description="The opening band a post title sits in.")

    write("hidden-archive-banner", "Archive banner",
          hero_band('<!-- wp:query-title {"type":"archive","className":"%s"} /-->' % c("statement")),
          inserter=False, description="The opening band an archive title sits in.")

    write("hidden-search-banner", "Search banner",
          hero_band('<!-- wp:query-title {"type":"search","className":"%s"} /-->' % c("statement")),
          inserter=False, description="The opening band search results sit under.")

    write("hidden-blog-heading", "Blog heading",
          hero_band(statement("Blog", caps=True)),
          inserter=False, description="The opening band of the posts page.")

    posts = (
        '<!-- wp:query {"queryId":0,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post",'
        '"order":"desc","orderBy":"date","inherit":true},"layout":{"type":"default"}} -->\n'
        '<div class="wp-block-query"><!-- wp:post-template {"style":{"spacing":{"blockGap":"%s"}},'
        '"layout":{"type":"default"}} -->\n%s\n<!-- /wp:post-template -->\n'
        '<!-- wp:query-no-results -->\n%s\n<!-- /wp:query-no-results -->\n'
        '<!-- wp:query-pagination {"paginationArrow":"arrow","className":"%s","layout":{"type":"flex","justifyContent":"center"}} -->\n'
        '<!-- wp:query-pagination-previous /-->\n'
        '<!-- wp:query-pagination-numbers /-->\n'
        '<!-- wp:query-pagination-next /-->\n'
        '<!-- /wp:query-pagination --></div>\n'
        '<!-- /wp:query -->' % (sp("50"), post_card(level=2),
                                paragraph("Nothing here yet. Try a search, or have a look at our work instead."),
                                c("pagination"))
    )
    layout = columns([
        column(posts, width="66.66%"),
        column('<!-- wp:template-part {"slug":"sidebar","area":"uncategorized"} /-->', width="33.33%"),
    ], gap="40", extra_class=c("with-sidebar"))
    write("hidden-posts-list", "Posts with sidebar",
          group(layout, align="full", padding={"top": "80", "bottom": "90"}, layout="constrained"),
          inserter=False, description="The post list used by the blog and every archive, beside the sidebar.")

    meta = flex_row("\n".join([
        '<!-- wp:post-date {"className":"%s"} /-->' % c("meta-date"),
        '<!-- wp:post-author-name {"className":"%s"} /-->' % c("meta-author"),
    ]), gap="30", extra_class=c("post-meta"))
    write("hidden-post-meta", "Post meta", meta, inserter=False,
          description="Date and author for a single post.")

    author = group(flex_row("\n".join([
        '<!-- wp:avatar {"size":90,"style":{"border":{"radius":"50%"}}} /-->',
        group("\n".join([
            '<!-- wp:post-author-name {"className":"%s"} /-->' % c("author-name"),
            '<!-- wp:post-author-biography /-->',
        ]), layout="constrained", gap="20"),
    ]), gap="40", wrap="nowrap", vertical="center"), layout="constrained",
        padding={"top": "50", "bottom": "50", "left": "40", "right": "40"},
        background="surface", style=c("panel"), extra_class=c("author-box"))
    write("hidden-author-box", "Author box", author, inserter=False,
          description="The author's photograph, name and biography under a post.")

    comments = (
        '<!-- wp:comments {"className":"%s"} -->\n'
        '<div class="wp-block-comments %s">\n'
        '<!-- wp:comments-title {"level":2} /-->\n'
        '<!-- wp:comment-template -->\n%s\n<!-- /wp:comment-template -->\n'
        '<!-- wp:comments-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->\n'
        '<!-- wp:comments-pagination-previous /-->\n'
        '<!-- wp:comments-pagination-numbers /-->\n'
        '<!-- wp:comments-pagination-next /-->\n'
        '<!-- /wp:comments-pagination -->\n'
        '<!-- wp:post-comments-form /-->\n'
        '</div>\n'
        '<!-- /wp:comments -->' % (c("comments"), c("comments"), flex_row("\n".join([
            '<!-- wp:avatar {"size":70,"style":{"border":{"radius":"50%"}}} /-->',
            group("\n".join([
                '<!-- wp:comment-content /-->',
                flex_row("\n".join([
                    '<!-- wp:comment-author-name /-->',
                    '<!-- wp:comment-date /-->',
                    '<!-- wp:comment-reply-link /-->',
                ]), gap="30", extra_class=c("comment-meta")),
            ]), layout="constrained", gap="20"),
        ]), gap="30", wrap="nowrap", vertical="top", extra_class=c("comment")))
    )
    write("hidden-comments", "Comments", comments, inserter=False,
          description="The comments area for a single post.")

    notfound = "\n".join([
        statement("That page has moved on"),
        paragraph("The address may be old, or the page may have been renamed. Try a search, "
                  "or start again from the home page."),
        '<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search the site",'
        '"buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"className":"%s"} /-->' % c("search-404"),
        buttons([underline_button("Back to the home page", link(""))]),
    ])
    write("hidden-404", "404 content", hero_band(notfound), inserter=False,
          description="What a visitor sees when nothing is there.")


# ---------------------------------------------------------------------------
# Whole pages
# ---------------------------------------------------------------------------
def ref(slug):
    return '<!-- wp:pattern {"slug":"%s/%s"} /-->' % (P, slug)


def build_pages():
    tail = ["facts", "studio-strip", "cta-band"]
    pages = {
        "page-home": ("Page: home", ["hero", "video", "works", "services-dark", "story"] + tail),
        "page-about": ("Page: about", ["heading-about", "video", "clients", "story"] + tail),
        "page-work": ("Page: work", ["heading-work", "works-page"] + tail),
        "page-services": ("Page: services", ["heading-services", "services-grid", "story"] + tail),
        "page-contact": ("Page: contact", ["heading-contact", "contact"]),
    }
    for p in PROJECTS:
        pages["page-project-%s" % p["slug"]] = ("Page: %s" % p["short"],
                                               ["heading-project-%s" % p["slug"], "project-%s" % p["slug"]] + tail)
    for slug, (title, refs) in pages.items():
        write(slug, title, "\n".join(ref(r) for r in refs), categories=PAGES,
              description="A complete %s page, built from the theme's sections." % title.split(": ")[1])


def main():
    os.makedirs(PATTERNS, exist_ok=True)
    for name in os.listdir(PATTERNS):
        if name.endswith(".php"):
            os.remove(os.path.join(PATTERNS, name))
    build_header()
    build_footer()
    build_sidebar()
    build_hidden()
    build_hero()
    build_page_headings()
    build_video()
    build_works()
    build_works("works-page", with_title=False)
    build_services_dark()
    build_services_grid()
    build_clients()
    build_story()
    build_facts()
    build_strip()
    build_band()
    build_contact()
    build_projects()
    build_blog_latest()
    build_pages()

    for slug in sorted(WRITTEN):
        print("  patterns/%s.php" % slug)
    print("\n%d patterns" % len(WRITTEN))


if __name__ == "__main__":
    main()
