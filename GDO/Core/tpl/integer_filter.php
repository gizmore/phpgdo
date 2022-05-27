<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Int;
/** @var $field GDT_Int **/
/** @var $f string **/
?>
<div>
<input
 name="<?=$f?>[f][<?=$field->name?>][min]"
 type="search"
 pattern="^[-\.0-9]*$"
 value="<?=html(@$field->filterVar($f)['min'])?>"
 placeholder="<?=t('from')?>"
 size="<?=round($field->bytes)?>" />
<input
 name="<?=$f?>[f][<?=$field->name?>][max]"
 type="search"
 pattern="^[-\.0-9]*$"
 value="<?=html(@$field->filterVar($f)['max'])?>"
 placeholder="<?=t('to')?>"
 size="<?=round($field->bytes)?>" />
</div>