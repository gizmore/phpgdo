<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Int;
use GDO\Table\GDT_Filter;

/** @var $field GDT_Int * */
/** @var $f GDT_Filter * */
?>
<div>
    <input
            name="<?=$f->name?>[<?=$field->name?>][min]"
            type="search"
            pattern="^[-\.0-9]*$"
            value="<?=html(@$field->filterVar($f)['min'])?>"
            placeholder="<?=t('from')?>"
            size="<?=round($field->bytes)?>"/>
    <input
            name="<?=$f->name?>[<?=$field->name?>][max]"
            type="search"
            pattern="^[-\.0-9]*$"
            value="<?=html(@$field->filterVar($f)['max'])?>"
            placeholder="<?=t('to')?>"
            size="<?=round($field->bytes)?>"/>
</div>
