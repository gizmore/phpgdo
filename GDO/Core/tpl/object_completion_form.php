<?php
namespace GDO\Core\tpl;
/** @var $field \GDO\Core\GDT_Object **/
?>
<div class="gdt-container<?=$field->classError()?> gdo-autocomplete">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <input
   <?=$field->htmlAutocompleteOff()?>
   data-config='<?=$field->displayConfigJSON()?>'
   <?=$field->htmlID()?>
   type="search"
   class="gdo-autocomplete-input"
   <?=$field->htmlPlaceholder()?>
   <?=$field->htmlRequired()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlFormName()?>
   <?=$field->htmlValue()?> />
  <input type="hidden" id="nocompletion_<?=$field->name?>" name="nocompletion_<?=$field->name?>" value="1" />
  <input type="hidden" id="completion-<?=$field->name?>" <?=$field->htmlValue()?>" />
  <?=$field->htmlError()?>
</div>
