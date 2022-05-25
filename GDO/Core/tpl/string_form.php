<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_String;
/** @var $field GDT_String **/
?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <input
  type="<?=$field->getInputType()?>"
  <?=$field->htmlID()?>
  <?=$field->htmlRequired()?>
  <?=$field->htmlPattern()?>
  <?=$field->htmlDisabled()?>
  maxlength="<?=$field->max?>"
  size="<?=min($field->max, 32)?>"
  <?=$field->htmlFormName()?>
  <?=$field->htmlValue()?> />
  <?=$field->htmlError()?>
</div>
