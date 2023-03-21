<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Select;
use GDO\Table\GDT_Filter;

/** @var $f GDT_Filter * */
/** @var $field GDT_Select * */
?>
<select name="<?=$f->name?>[<?=$field->name?>]">
    <option><?=t('sel_all')?></option>
	<?php
	foreach ($field->initChoices() as $var => $value) : ?>
        <option value="<?=$var?>"><?=is_string($value) ? $field->displayVar($var) : $value->renderOption()?></option>
	<?php
	endforeach; ?>
</select>
