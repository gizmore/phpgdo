<?php
namespace GDO\Language\tpl;

use GDO\Language\GDO_Language;

/** @var $language GDO_Language * */
?>
<span class="gdt-choice">
<?php
if ($language) :
	$href = GDO_WEB_ROOT . "GDO/Language/img/{$language->getID()}.png";
	?>
    <img
            class="gdo-language"
            alt="<?=$language->getID()?>"
            title="<?=$language->renderName()?>"
            src="<?=$href?>"/>
	<?=$language->renderName()?>
<?php
else :
	$href = GDO_WEB_ROOT . 'GDO/Language/img/zz.png';
	?>
    <img
            class="gdo-language"
            alt="lang"
            src="<?=$href?>"/>
	<?=t('unknown_language')?>
<?php
endif; ?>
</span>
