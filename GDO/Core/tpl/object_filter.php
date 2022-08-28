<?php
namespace GDO\Core\tpl;
/** @var $f \GDO\Table\GDT_Filter **/
/** @var $field \GDO\Core\GDT_Int **/
?>
<input
 name="<?=$f->name?>[<?=$field->name?>]"
 type="text"
 value="<?=html($field->filterVar($f))?>"
 size="5"
 placeholder="<?=t('object_filter')?>"  />
