#!/bin/bash
#
# This script updates a phpgdo repository and it's submodules.
# Triggered by gdo_update.sh.
#
# @author gizmore
# @version 7.0.2
# @since 6.0.1
#
set -euo pipefail
orgpath="$(pwd)"
mpath="$(realpath "$1")"
cd "$(dirname "$0")"
#LANG=en_GB
#LC_ALL=en_GB

# Update the phpgdo module
echo "Updating module $2..."
echo "Folder: $1"
cd $mpath
git checkout main 2>/dev/null || true
git checkout master 2>/dev/null || true
git reset --hard
git pull

# Update submodules
echo "Updating and fixing submodules."
git submodule foreach git reset --hard
git submodule foreach git checkout main 2>/dev/null || true
git submodule foreach git checkout master 2>/dev/null || true
git submodule foreach git pull

# Save the update
cd $mpath
git commit -am "GDOv7 Autosync $2"
git push

cd $orgpath
