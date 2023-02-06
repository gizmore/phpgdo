#!/bin/bash
#
# This script updates a phpgdo repository and it's submodules.
# Triggered by gdo_update.sh.
#
# @author gizmore
# @version 7.0.2
# @since 6.0.1
#
#
set -euo pipefail
cd "$(dirname "$0")"
#LANG=en_GB
#LC_ALL=en_GB
# Update phpgdo module
echo "$2"
echo "Update module folder $1."
git checkout main 2>/dev/null || true
git checkout master 2>/dev/null || true
git reset --hard
git pull

# Update submodules
echo "Updating and fixing submodules."
git submodule foreach git checkout main 2>/dev/null || true
git submodule foreach git checkout master 2>/dev/null || true
git submodule foreach git reset --hard
git submodule foreach git pull

