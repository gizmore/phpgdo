#!/bin/bash
cd "$(dirname "$0")"
set -euo pipefail

php gdo_adm.php "$@"
