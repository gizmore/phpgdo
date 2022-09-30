<?php
namespace GDO\Core\tpl;
/** @var $field \GDO\Core\GDT_ComboBox **/
?>
<div class="gdt-container gdo-completion<?= $field->classError(); ?>">
  <label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
  <input
   class="gdo-autocomplete-input"
   data-config='<?=$field->displayConfigJSON()?>'
   type="<?=$field->getInputType()?>"
   <?=$field->htmlID()?>
   <?=$field->htmlFocus()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlPattern()?>
   <?=$field->htmlDisabled()?>
   min="<?=$field->min?>"
   max="<?=$field->max?>"
   size="<?=min($field->max, 32)?>"
   <?=$field->htmlName()?>
   <?=$field->htmlPlaceholder()?>
   value="<?=$field->renderVar()?>" />
  <?=$field->htmlError()?>
  <input type="hidden" id="completion-<?=$field->name?>" value="<?=$field->renderVar()?>" />
</div>
