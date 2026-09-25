#!/usr/bin/env bash
#
# Capture the HTML template and the theme at one width and put them side by
# side in .dev/compare/.
#
#   bash .dev/compare.sh <name> <template page> <theme path> [width]
#   bash .dev/compare.sh home index.html / 1440
#
set -euo pipefail
here="$( cd "$( dirname "$0" )" && pwd )"
name="$1"; tpl="$2"; path="$3"; width="${4:-1440}"
TPL="${TEMPLATE_URL:-https://preview.colorlib.com/theme/creativeagency2}"
WP="${WP_URL:-http://127.0.0.1:9492}"
mkdir -p "$here/compare/raw"
SETTLE=11000 node "$here/capture.mjs" "$TPL/$tpl" "$here/compare/raw/tpl-$name-$width.png" "$width" &
node "$here/capture.mjs" "$WP$path" "$here/compare/raw/wp-$name-$width.png" "$width" &
wait
python3 "$here/side-by-side.py" "$here/compare/raw/tpl-$name-$width.png" "$here/compare/raw/wp-$name-$width.png" \
	"$here/compare/$name-$width.jpg" "$( [ "$width" -le 480 ] && echo 0.6 || echo 0.4 )"
