<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Object;

/** @var $field GDT_Object * */
?>
<div class="gdt-container<?=$field->classError()?> gdo-autocomplete">
    <label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <span>
  <input
<?=$field->htmlAutocompleteOff()?>
	  <?=$field->htmlConfig()?>
	  <?=$field->htmlID()?>
   type="search"
   class="gdo-autocomplete-input"
<?=$field->htmlFocus()?>
	  <?=$field->htmlPlaceholder()?>
	  <?=$field->htmlRequired()?>
	  <?=$field->htmlDisabled()?>
	  <?=$field->htmlName()?>
	  <?=$field->htmlValue()?> />
  <input type="hidden" id="nocompletion_<?=$field->name?>" name="nocompletion_<?=$field->name?>" value="1"/>
  <input type="hidden" id="completion-<?=$field->name?>" <?=$field->htmlValue()?> />
 </span>
	<?=$field->htmlError()?>
</div>
