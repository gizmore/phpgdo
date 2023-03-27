<?php
declare(strict_types=1);
namespace GDO\Core\tpl;

use GDO\Core\GDO;
use GDO\Core\GDT_Select;

/** @var GDT_Select $field * */
if (isset($field->completionHref))
{
	$field->addClass('gdo-autocomplete');
}
?>
<label<?=$field->htmlForID()?>>
	<?=$field->htmlIcon()?>
	<?=$field->renderLabel()?>
</label>
<select
	<?=$field->htmlID()?>
	<?=$field->htmlAttributes()?>
	<?=$field->htmlFocus()?>
	<?php
	if ($field->hasCompletion()) : ?>
		<?=$field->htmlConfig()?>
	<?php
	endif; ?>
	<?=$field->htmlName()?>
	<?=$field->htmlMultiple()?>
	<?=$field->htmlDisabled()?>>
	<?php
	if ($field->hasEmptyLabel()) : ?>
        <option value="<?=$field->emptyVar?>"<?=$field->htmlSelected($field->emptyVar)?>><?=$field->renderEmptyLabel()?></option>
	<?php
	endif; ?>
	<?php
	if ($field->hasCompletion()) : ?>
		<?php
		if ($choice = $field->getValue()) : ?>
            <option value="<?=html($field->getVar())?>"<?=$field->htmlSelected($field->getVar())?>><?=$choice instanceof GDO ? $choice->renderOption() : $field->displayChoice($field->getVar())?></option>
		<?php
		endif; ?>
	<?php
	else : ?>
		<?php
		foreach ($field->initChoices() as $var => $choice) : ?>
			<?php $vv = (string) $var; ?>
            <option<?=$field->htmlChoiceVar($vv, $choice)?><?=$field->htmlSelected($vv)?>><?= ($choice instanceof GDO) ? $choice->renderOption() : $field->displayVar($vv) ?></option>
		<?php
		endforeach; ?>
	<?php
	endif; ?>
</select>
