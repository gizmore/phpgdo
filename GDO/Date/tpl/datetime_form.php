<?php
namespace GDO\Date\tpl;
use GDO\Date\GDT_DateTime;
/** @var $field GDT_DateTime **/
?>
<div class="gdt-container<?=$field->classError()?>">
<label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<input<?=$field->htmlID()?> type="datetime-local"
 autocomplete="off"
 value="<?=tt($field->getVar(), 'local')?>"
<?=$field->htmlName()?>
<?=$field->htmlDisabled()?> />
<?=$field->htmlError()?>
</div>
