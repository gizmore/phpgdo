<?php
namespace GDO\Date\tpl\form;
use GDO\Date\GDT_Date
/**
 * @var $field GDT_Date
 */
?>
<div class="gdt-container<?=$field->classError()?>">
<label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
<input<?=$field->htmlID()?> type="date"
<?=$field->htmlName()?>
<?=$field->htmlValue()?>
<?=$field->htmlDisabled()?> />
 <?=$field->htmlError()?>
</div>
