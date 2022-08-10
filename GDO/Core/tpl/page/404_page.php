<?php
namespace GDO\Core\tpl\page;
use GDO\Net\URL;
use GDO\UI\GDT_Error;
/**
 * @var $url URL
 */
$error = GDT_Error::make()->title('file_not_found');
$error->text('err_file_not_found', [html($url->raw)]);
echo $error->renderHTML();
