<?php
namespace GDO\Date\tpl\form;

use GDO\Date\GDT_Duration;

/** @var $field GDT_Duration * */
?>
<div class="gdt-container<?=$field->classError()?>">
    <label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <input<?=$field->htmlID()?> type="text"
		<?=$field->htmlName()?>
		<?=$field->htmlDisabled()?>
		<?=$field->htmlRequired()?>
                                value="<?=$field->getVar()?>"/>
	<?=$field->htmlError();?>
</div>
