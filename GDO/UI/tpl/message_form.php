<?php
namespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Message **/
?>
<div class="gdt-container<?=$field->classError()?>">
<label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<div class="<?=$field->classEditor()?>"<?=$field->htmlID()?>>
<textarea rows="6"
<?=$field->htmlFocus()?>
<?=$field->htmlFormName()?>
<?=$field->htmlRequired()?>
<?=$field->htmlDisabled()?>><?=html($field->getVar())?></textarea>
 <?=$field->htmlError()?>
</div>
</div>
