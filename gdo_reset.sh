#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

CORE="$(dirname "$0")"

echo "Resetting all repositories with git reset --hard."

find . -type d -name .git -prune -print \
| sed 's#/.git$##' \
| while read -r repo; do
    echo "=== $repo ==="
    (
      cd "$repo" || exit
      git reset --hard
      git submodule foreach --recursive 'git reset --hard'
    )
  done
