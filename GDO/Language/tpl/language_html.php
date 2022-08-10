<?php
namespace GDO\Language\tpl;
/**
 * @var $language \GDO\Language\GDO_Language
 */
?>
<div class="gdo-language">
 <img
  alt="<?=$language->renderName()?>"
  title="<?=$language->renderName()?>"
  src="<?=html($language->hrefFlag())?>" />
</div>
