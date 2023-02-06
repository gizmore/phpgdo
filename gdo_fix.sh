#!/bin/bash
#
# This script updates phpgdo and all installed modules.
#
# @author gizmore
# @version 7.0.2
# @since 6.0.1
#
set -euo pipefail
cd "$(dirname "$0")"
#
gdopath="$(dirname "$0")"
#LANG=en_GB
#LC_ALL=en_GB
#
echo "1) Updating phpgdo."
bash gdo_update_module.sh $gdopath "Core"
#
# Other modules
#
echo "2) Updating all modules with threading."
find ./GDO/ -maxdepth 2 -iname ".git" -exec bash gdo_update_module.sh "{}/../" "{}" \;
#
# Installation and upgrades
#
cd $gdopath
#
echo "3) Rewriting config."
php ./gdo_adm.php confgrade
#
echo "4) Updating gdo modules."
php gdo_adm.php update
#
echo "5) Updating assets."
bash ./gdo_yarn.sh
#
echo "6) Triggering post install scripts."
bash ./gdo_post_install.sh
#
echo "7) Done!"
