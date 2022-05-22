#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"
message="'$*'"

echo "GDOv7 sync.sh: Sync message: $message"

if [ "'$1'" -eq  "all" ]
    then echo "1. DO only push all repos" && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git push" \;
    else echo "2. DO git commit & push all repos" && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git add -A . && git commit -am $message && git push" \;
fi
