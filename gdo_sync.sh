#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"
message="'$*'"

echo "GDOv7 sync.sh: Sync message: $message"
echo "YOU SURE?!"

sleep 5

if [ "$message" == "all" ]
    then echo "DO only push all repos" && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git push" \;
    else echo "DO git commit & push all repos" && php provider_update.php && find . -iname ".git" -type d -exec sh -c "cd $CORE && cd {} && cd .. && pwd && git add -A . && git commit -am \"$message\" && git push" \;
fi
