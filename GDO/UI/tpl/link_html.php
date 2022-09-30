<?php
namespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Link **/
?>
<span class="<?=$field->htmlClass()?>"><a
<?=$field->htmlDisabled()?>
<?=$field->htmlAttributes()?>
<?=$field->htmlTarget()?>
<?=$field->htmlHREF()?>
<?=$field->htmlRelation()?>><?=$field->htmlIcon()?>
<?php if ($field->hasText()) : ?>
<?=$field->renderText()?>
<?php else : ?>
<?=html(isset($field->href)?$field->href:'---')?>
<?php endif; ?>
</a></span>
