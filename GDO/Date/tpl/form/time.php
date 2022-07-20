<?php /** @var $field \GDO\Date\GDT_Time **/ ?>
<div class="gdt-container<?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <input
   <?=$field->htmlID()?>
   type="time"
   <?=$field->htmlFormName()?>
   value="<?=$field->renderVar()?>"
   <?=$field->htmlDisabled()?> />
  <?=$field->htmlError()?>
</div>
