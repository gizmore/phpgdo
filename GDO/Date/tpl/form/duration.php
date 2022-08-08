<?php
namespace GDO\Date\tpl\form;
/** @var $field \GDO\Date\GDT_Duration **/
?>
<div class="gdt-container<?=$field->classError()?>">
<label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<input<?=$field->htmlID()?> type="text"
<?=$field->htmlFormName()?>
<?=$field->htmlDisabled()?>
<?=$field->htmlRequired()?>
 value="<?=$field->getVar()?>" />
 <?= $field->htmlError(); ?>
</div>
