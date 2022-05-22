<?php
use GDO\Core\GDT_Select;
/** @var $field GDT_Select **/
if (isset($field->completionHref))
{
    $field->addClass('gdo-autocomplete');
}
?>
  <?=$field->htmlIcon()?>
  <label <?=$field->htmlForID()?>><?=$field->renderLabel()?></label>
  <select
   <?=$field->htmlID()?>
   <?=$field->htmlAttributes()?>
   <?=$field->htmlMultiple()?>>
   <?=$field->htmlDisabled()?>>
<?php if (isset($field->emptyLabel)) : ?>
	<option value="<?=$field->emptyValue?>"<?=$field->htmlSelected($field->emptyValue)?>><?=$field->displayEmptyLabel()?></option>
<?php endif; ?>
<?php foreach ($field->choices as $var => $choice) : ?>
	<option value="<?=html($var)?>"<?=$field->htmlSelected($var);?>><?=$field->displayChoice($choice)?></option>
<?php endforeach; ?>
  </select>