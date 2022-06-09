<?php
namespace GDO\Core\tpl\page;
use GDO\Net\URL;
/**
 * @var $url URL
 */
?>
<h2><?=t('file_not_found')?></h2>
<p><?=t('err_file_not_found', [html($url->raw)])?></p>

