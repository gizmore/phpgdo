<?php
namespace GDO\Language\tpl;

use GDO\Language\GDO_Language;

/**
 * @var $language GDO_Language
 */
?>
<div class="gdo-language">
    <img
            alt="<?=$language->renderName()?>"
            title="<?=$language->renderName()?>"
            src="<?=html($language->hrefFlag())?>"/>
</div>
