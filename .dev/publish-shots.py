#!/usr/bin/env python3
"""Turn .dev/publish-shots.mjs's PNG captures into the product page's JPEGs.

    python3 .dev/publish-shots.py [rawdir]

Writes .dev/publish/images/creative-agency-block-theme-<what>.jpg (1140 wide,
q82, progressive) and the listing card creative-agency-free-design-agency-
wordpress-theme.jpg (1200x800). The palettes image tiles the same stretch of the
home page under all six palettes, three across, each tile labelled.
"""
import os
import sys

from PIL import Image, ImageDraw, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
RAW = sys.argv[1] if len(sys.argv) > 1 else os.path.join(ROOT, ".dev/publish/raw")
OUT = os.path.join(ROOT, ".dev/publish/images")
os.makedirs(OUT, exist_ok=True)
PREFIX = "creative-agency-block-theme-"


def save(im, name):
    path = os.path.join(OUT, name)
    im.convert("RGB").save(path, "JPEG", quality=82, progressive=True, optimize=True)
    print(name, im.size, os.path.getsize(path) // 1024, "KB")


def width(im, w=1140):
    return im.resize((w, round(im.size[1] * w / im.size[0])), Image.LANCZOS)


for shot in ["home", "projects", "services", "contact-form", "blog", "project-page", "dark-mode"]:
    save(width(Image.open(os.path.join(RAW, shot + ".png"))), PREFIX + shot + ".jpg")

save(Image.open(os.path.join(RAW, "card.png")).resize((1200, 800), Image.LANCZOS),
     "creative-agency-free-design-agency-wordpress-theme.jpg")

names = ["Azure", "Violet", "Coral", "Emerald", "Ink", "Midnight"]
files = sorted(f for f in os.listdir(RAW) if f.startswith("palette-"))
gap, cols, tw = 12, 3, 372
tiles = [width(Image.open(os.path.join(RAW, f)), tw) for f in files]
th = tiles[0].size[1]
label_h = 34
sheet = Image.new("RGB", (cols * tw + (cols - 1) * gap, 2 * (th + label_h) + gap), "white")
draw = ImageDraw.Draw(sheet)
try:
    font = ImageFont.truetype("/System/Library/Fonts/Supplemental/Arial Bold.ttf", 17)
except OSError:
    font = ImageFont.load_default()
for i, tile in enumerate(tiles):
    x = (i % cols) * (tw + gap)
    y = (i // cols) * (th + label_h + gap)
    sheet.paste(tile, (x, y))
    draw.text((x + 2, y + th + 8), names[i], fill="#222222", font=font)
save(sheet, PREFIX + "colour-palettes.jpg")
