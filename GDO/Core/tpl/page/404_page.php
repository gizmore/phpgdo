<?php
namespace GDO\Core\tpl\page;
use GDO\Net\URL;
use GDO\UI\GDT_Error;
use GDO\UI\TextStyle;
/**
 * @var $url URL
 */
$error = GDT_Error::make()->title('file_not_found')->code(404);
$error->text('err_file_not_found', [TextStyle::boldi(html($url->raw))]);
echo $error->render();
