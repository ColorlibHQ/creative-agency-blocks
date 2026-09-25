#!/usr/bin/env bash
#
# The rendered checks, over every kind of page the theme builds, in both
# schemes. Run against a Playground started from .dev/blueprint.json.
#
#   WP_URL=http://127.0.0.1:9492 bash .dev/check-all.sh
#
set -uo pipefail
here="$( cd "$( dirname "$0" )" && pwd )"
export WP_URL="${WP_URL:-http://127.0.0.1:9492}"
post="$( curl -s -b playground_auto_login_already_happened=1 "$WP_URL/?rest_route=/wp/v2/posts&per_page=1" \
	| python3 -c 'import json,sys; print(json.load(sys.stdin)[0]["link"])' | sed "s#$WP_URL##" )"
PAGES="/ /about/ /work/ /work/nookdesk/ /work/whisk/ /work/harrow-and-pine/ /work/arcade-club/ /services/ /blog/ /contact/ $post /category/studio/ /?s=design /no-such-page/"
fail=0

for scheme in light dark; do
	for p in $PAGES; do
		if [ "$scheme" = dark ]; then
			out="$( CREATIVE_AGENCY_DARK=1 CREATIVE_AGENCY_URL="$p" node "$here/contrast-rendered.mjs" 2>&1 )" || fail=1
		else
			out="$( CREATIVE_AGENCY_URL="$p" node "$here/contrast-rendered.mjs" 2>&1 )" || fail=1
		fi
		echo "contrast $scheme: $out" | head -8
	done
done

paths="$( echo $PAGES | tr ' ' ',' )"
CREATIVE_AGENCY_PATHS="$paths" node "$here/overflow-check.mjs" 2>&1 | grep -v " — clean$" || true
CREATIVE_AGENCY_PATHS="$paths" node "$here/overflow-check.mjs" >/dev/null 2>&1 || fail=1
echo "overflow: $( [ $fail = 0 ] && echo ok )"
CREATIVE_AGENCY_PATHS="$paths" node "$here/alignment-check.mjs" 2>&1 || fail=1
PATHS="$paths" node "$here/button-boundary.mjs" 2>&1 || fail=1
CREATIVE_AGENCY_DARK=1 PATHS="$paths" node "$here/button-boundary.mjs" 2>&1 || fail=1
python3 "$here/dead-selectors.py" 2>&1 | tail -1 || fail=1

exit $fail
