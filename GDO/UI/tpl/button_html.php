<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Button;
/**
 * @var $field GDT_Button
 */
?>
<div class="gdt-button">
  <a <?=$field->htmlHREF()?>
   <?=$field->htmlDisabled()?>
  ><?=$field->renderText()?></a>
</div>
