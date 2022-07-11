<?php
namespace GDO\Core\tpl;
/** @var $f string **/
/** @var $field \GDO\Core\GDT_Int **/
?>
<input
 name="<?=$f?>[f][<?=$field->name?>]"
 type="text"
 value="<?=html($field->filterVar($f))?>"
 size="5"
 placeholder="<?=t('object_filter')?>"  />
