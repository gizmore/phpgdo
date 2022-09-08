<?php
namespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Message **/
?>
<div class="gdt-container<?=$field->classError()?>">
<label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<div class="<?=$field->classEditor()?>"<?=$field->htmlID()?>>
<textarea
<?=$field->htmlRows()?>
<?=$field->htmlFocus()?>
<?=$field->htmlName()?>
<?=$field->htmlRequired()?>
<?=$field->htmlDisabled()?>><?=html($field->getVarInput())?></textarea>
<?=$field->htmlError()?>
</div>
</div>
