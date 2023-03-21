<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Object;

/** @var $field GDT_Object * */ ?>
<div class="gdt-container<?=$field->classError()?>">

    <label <?=$field->htmlForID()?>><?=$field->htmlIcon()?><?=$field->renderLabel()?></label>
    <input
		<?=$field->htmlID()?>
            type="text"
		<?=$field->htmlFocus()?>
		<?=$field->htmlName()?>
		<?=$field->htmlValue()?>
		<?=$field->htmlRequired()?>
		<?=$field->htmlDisabled()?> />
	<?=$field->htmlError()?>
</div>
