<?php
use GDO\Core\GDT_Select;
/** @var $field GDT_Select **/
if (isset($field->completionHref))
{
    $field->addClass('gdo-autocomplete');
}
?>
<div class="gdt-container <?=$field->classError()?>">
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <select
   <?=$field->htmlID()?>
   <?=$field->htmlAttributes()?>
<?php if ($field->completionHref) : ?>
    data-config='<?=$field->displayConfigJSON()?>'
<?php endif; ?>
   <?=$field->htmlFormName()?>
   <?=$field->htmlMultiple()?>
   <?=$field->htmlDisabled()?>>
<?php if ($field->emptyLabel) : ?>
	<option value="<?=$field->emptyValue?>"<?=$field->htmlSelected($field->emptyValue)?>><?=$field->displayEmptyLabel()?></option>
<?php endif; ?>
<?php foreach ($field->choices as $var => $choice) : ?>
	<option value="<?=html($var)?>"<?=$field->htmlSelected($var);?>><?=$field->displayChoice($choice)?></option>
<?php endforeach; ?>
  </select>
  <?=$field->htmlError()?>
</div>
