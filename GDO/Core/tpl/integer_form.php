<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Int;
/** @var $field GDT_Int **/
?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="number"
   min="<?=$field->min;?>"
   max="<?=$field->max;?>"
   step="<?=$field->step?>"
   <?=$field->htmlFormName()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlRequired()?>
   value="<?=$field->getVar()?>" />
  <?=$field->htmlError()?>
</div>
