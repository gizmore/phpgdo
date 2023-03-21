<?php
namespace GDO\Date\tpl\form;

use GDO\Date\GDT_Time;

/** @var $field GDT_Time * */
?>
<div class="gdt-container<?=$field->classError()?>">
    <label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <input<?=$field->htmlID()?> type="time"
		<?=$field->htmlName()?>
                                value="<?=$field->renderVar()?>"
		<?=$field->htmlDisabled()?> />
	<?=$field->htmlError()?>
</div>
