<?php
namespace GDO\Language\tpl\cell;
use GDO\Language\GDO_Language;
/**
 * @var $language GDO_Language
 */
$href = GDO_WEB_ROOT . 'GDO/Language/img/' . $language->getID() . '.png';
?>
<img
class="gdo-language"
	alt="<?= $language->renderName(); ?>"
	title="<?= $language->renderName(); ?>"
	src="<?=html($href)?>" />
	