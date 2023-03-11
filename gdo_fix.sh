#!/bin/bash
#
# This script updates phpgdo and all installed modules.
#
# @author gizmore
# @version 7.0.2
# @since 6.0.1
#
set -euo pipefail
#
orgpath="$(pwd)"
gdopath="$(dirname "$0")"
#LANG=en_GB
#LC_ALL=en_GB
#
cd $gdopath
echo "1) Updating phpgdo core."
bash gdo_update_module.sh $gdopath "Core" "1"
#
# Other modules
#
cd $gdopath
echo "2) Updating all modules."
find ./GDO/ -maxdepth 3 -iname ".git" -exec bash gdo_update_module.sh "{}/../" "{}" "0" \;
#
# Installation and upgrades
#
cd $gdopath
echo "3) Rewriting config."
php gdo_adm.php confgrade
#
cd $gdopath
echo "4) Updating gdo modules."
php gdo_adm.php --quiet update
#
cd $gdopath
echo "5) Updating assets."
bash ./gdo_yarn.sh
#
cd $gdopath
echo "6) Triggering post install scripts."
bash ./gdo_post_install.sh
#
echo "7) Done!"
cd $orgpath
