<?php
namespace GDO\Date\tpl;
/** @var $field \GDO\Date\GDT_Timestamp **/
/** @var $f \GDO\Table\GDT_Filter **/
?>
<input
 name="<?=$f->name?>[<?=$field->name?>][min]"
 type="search"
 pattern="^[-\.0-9/ :aAmMpP]*$"
 value="<?=$field->displayVar(@$field->filterVar($f)['min'])?>"
 placeholder="<?=t('from')?>" />
<input
 name="<?=$f->name?>[<?=$field->name?>][max]"
 type="search"
 pattern="^[-\.0-9/ :aAmMpP]*$"
 value="<?=$field->displayVar(@$field->filterVar($f)['max'])?>"
 placeholder="<?=t('to')?>" />
 