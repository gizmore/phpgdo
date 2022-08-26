<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Checkbox;
use GDO\Table\GDT_Filter;
/** @var $f GDT_Filter **/
/** @var $field GDT_Checkbox **/
?>
<?php $val = $field->filterVar($f); ?>
<select name="<?=$f->name?>[<?=$field->name?>]" onchange="submit()">
  <option value="" <?= $val === '' ? 'selected="selected"' : ''; ?>><?=t('sel_all')?></option>
  <option value="1" <?= $val === '1' ? 'selected="selected"' : ''; ?>><?=t('sel_checked')?></option>
  <option value="0" <?= $val === '0' ? 'selected="selected"' : ''; ?>><?=t('sel_unchecked')?></option>
</select>
