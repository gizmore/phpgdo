<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_Int;
use GDO\Table\GDT_Filter;

/** @var $f GDT_Filter * */
/** @var $field GDT_Int * */
?>
<input
        name="<?=$f->name?>[<?=$field->name?>]"
        type="text"
        value="<?=html($field->filterVar($f))?>"
        size="5"
        placeholder="<?=t('object_filter')?>"/>
