<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Int;

/** @var $field GDT_Int * */
?>
<div class="gdt-number gdt-container<?=$field->classError()?>">
    <label<?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <input
		<?=$field->htmlFocus()?>
		<?=$field->htmlID()?>
            type="number"
		<?=$field->htmlConfig()?>
            min="<?=$field->min?>"
            max="<?=$field->max?>"
            step="<?=$field->step?>"
		<?=$field->htmlName()?>
		<?=$field->htmlDisabled()?>
		<?=$field->htmlRequired()?>
		<?=$field->htmlFocus()?>
		<?=$field->htmlValue()?>>
	<?=$field->htmlError()?>
</div>
