<?php
namespace GDO\Core\tpl;

use GDO\Core\GDT_String;
use GDO\Table\GDT_Filter;

/** @var $field GDT_String * */
/** @var $f GDT_Filter * */
?>
<input
        name="<?=$f->name?>[<?=$field->name?>]"
        type="search"
        size="<?=min($field->min, 16)?>"
        value="<?=html($field->filterVar($f))?>"
        placeholder="<?=t('text')?>"/>
