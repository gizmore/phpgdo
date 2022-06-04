<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Select;
/** @var $field GDT_Select **/
if (isset($field->completionHref))
{
    $field->addClass('gdo-autocomplete');
}
?>
  <label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
  <select
   <?=$field->htmlID()?>
   <?=$field->htmlAttributes()?>
<?php if ($field->hasCompletion()) : ?>
    data-config='<?=$field->displayConfigJSON()?>'
<?php endif; ?>
   <?=$field->htmlFormName()?>
   <?=$field->htmlMultiple()?>
   <?=$field->htmlDisabled()?>>
<?php if ($field->hasEmptyLabel()) : ?>
	<option value="<?=$field->emptyVar?>"<?=$field->htmlSelected($field->emptyVar)?>><?=$field->renderEmptyLabel()?></option>
<?php endif; ?>
<?php if ($field->hasCompletion()) : ?>
	<option value="<?=html($field->getVar())?>"<?=$field->htmlSelected($field->getVar())?>><?=$field->displayChoice($field->getValue())?></option>
<?php else : ?>
<?php foreach ($field->getChoices() as $var => $choice) : ?>
	<option<?=$field->htmlChoiceVar($var, $choice)?><?=$field->htmlSelected($var)?>><?=$field->displayChoice($choice)?></option>
<?php endforeach; ?>
<?php endif; ?>
  </select>
