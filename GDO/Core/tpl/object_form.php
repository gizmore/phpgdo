<?php
namespace GDO\Core\tpl;
/** @var $field \GDO\Core\GDT_Object **/ ?>
<div class="gdt-container<?=$field->classError()?>">
  
  <label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="number"
   step="1"
   <?=$field->htmlFocus()?>
   <?=$field->htmlFormName()?>
   value="<?=$field->renderVar()?>"
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
