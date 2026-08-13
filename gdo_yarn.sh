#!/bin/bash
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "All modules: yarn install"
echo "Thanks to greycat@freenode#bash"
status=0
for d in GDO/*/; do
	[[ -f "${d}package.json" ]] || continue
	echo "$d"
	(cd "$d" && yarn install) || status=1
done
exit "$status"
