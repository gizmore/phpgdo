<?php
namespace GDO\Core\tpl;
/** @var $f \GDO\Table\GDT_Filter **/
/** @var $field \GDO\Core\GDT_Select **/
?>
<select name="<?=$f->name?>[<?=$field->name?>]">
 <option><?=t('sel_all')?></option>
<?php foreach($field->getChoices() as $var => $value) : ?>
 <option value="<?=$var?>"><?=is_string($value)?html($value):$value->renderOption()?></option>
<?php endforeach; ?>
</select>
