<?php
namespace GDO\Core\tpl;
/** @var $field \GDO\Core\GDT_String **/
/** @var $f string **/
?>
<input
 name="<?=$f?>[f][<?=$field->name?>]"
 type="search"
 size="<?=min($field->max, 16)?>"
 value="<?=html($field->filterVar($f))?>"
 placeholder="<?=t('text')?>" />
