<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Select;
/** @var $field GDT_Select **/
if (isset($field->completionHref))
{
    $field->addClass('gdo-autocomplete');
}
?>
<div class="gdt-container <?=$field->classError()?>">
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
<?php foreach ($field->choices as $var => $choice) : ?>
	<option value="<?=html($var)?>"<?=$field->htmlSelected($var);?>><?=$field->displayChoice($choice)?></option>
<?php endforeach; ?>
<?php else : ?>
	<option value="<?=html($field->getVar())?>"<?=$field->htmlSelected($field->getVar())?>><?=$field->displayChoice($field->getValue())?></option>
<?php endif; ?>
  </select>
  <?=$field->htmlError()?>
</div>
