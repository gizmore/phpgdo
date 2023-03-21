<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_String;

/** @var $field GDT_String * */
?>
<div class="gdt-container<?=$field->classError()?>">
    <label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <input
		<?=$field->htmlFocus()?>
            type="<?=$field->getInputType()?>"
		<?=$field->htmlID()?>
		<?=$field->htmlRequired()?>
		<?=$field->htmlPattern()?>
		<?=$field->htmlDisabled()?>
            maxlength="<?=$field->max?>"
            size="<?=min($field->max, 64)?>"
		<?=$field->htmlName()?>
		<?=$field->htmlValue()?> />
	<?=$field->htmlError()?>
</div>
