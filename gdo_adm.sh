#!/bin/bash
set -euo pipefail
cd "$(dirname "$0")"

php gdo_adm.php "$@"
