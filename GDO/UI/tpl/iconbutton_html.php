<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_IconButton;
/**
 * @var $field GDT_IconButton
 */
?>
<div class="gdt-button gdt-icon-button">
  <a <?=$field->htmlHREF()?>><?=$field->htmlIcon()?> <?=$field->renderText()?></a>
</div>
