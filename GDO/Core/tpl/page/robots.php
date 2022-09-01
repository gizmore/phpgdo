<?php
namespace GDO\Core\tpl\page;
use GDO\Core\Module_Core;
?>
#
# <?=sitename()?> robots.txt
#
# powered by phpgdo-<?=Module_Core::GDO_REVISION?>.
#
# (c) 2022 - gizmore@wechall.net (C.B.)
#

User-agent: *

Disallow: /robots.txt
Disallow: /security.txt

Disallow: /assets/
Disallow: /bin/
Disallow: /DOCS/
Disallow: /eclipse/
Disallow: /files/
Disallow: /files_test/
Disallow: /GDO/
Disallow: /install/
Disallow: /protected/
Disallow: /temp/
Disallow: /temp_test/
Disallow: /vendor/
