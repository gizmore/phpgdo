<?php
namespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Message **/
?>
<div class="gdt-container<?=$field->classError()?>">
<label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<div class="wysiwyg"<?=$field->htmlID()?>>
<textarea rows="6"
class="<?=$field->classEditor()?>"
<?=$field->htmlFormName()?>
<?=$field->htmlRequired()?>
<?=$field->htmlDisabled()?>><?=html($field->getVar())?></textarea>
<?=$field->htmlError()?>
</div>
</div>
