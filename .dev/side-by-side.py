#!/usr/bin/env python3
"""Put two full-page captures side by side, labelled, on one canvas.

    python3 .dev/side-by-side.py template.png theme.png out.jpg [scale]

Tall pages are scaled down together, so the same section sits at roughly the
same height on both sides and a difference in rhythm is visible at a glance.
"""
import sys
from PIL import Image, ImageDraw

left, right, out = sys.argv[1:4]
scale = float(sys.argv[4]) if len(sys.argv) > 4 else 0.5
a, b = Image.open(left).convert("RGB"), Image.open(right).convert("RGB")
gap, label = 24, 36
w = a.width + b.width + gap
h = max(a.height, b.height) + label
canvas = Image.new("RGB", (w, h), (200, 200, 200))
canvas.paste(a, (0, label))
canvas.paste(b, (a.width + gap, label))
d = ImageDraw.Draw(canvas)
d.text((10, 10), "HTML template  " + left.rsplit("/", 1)[-1], fill=(0, 0, 0))
d.text((a.width + gap + 10, 10), "Block theme  " + right.rsplit("/", 1)[-1], fill=(0, 0, 0))
canvas = canvas.resize((int(w * scale), int(h * scale)), Image.LANCZOS)
canvas.save(out, quality=82)
print(out, canvas.size)
