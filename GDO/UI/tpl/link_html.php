<?php
namespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Link **/
?>
<span class="<?=$field->htmlClass()?>"><a
<?=$field->htmlDisabled()?>
<?=$field->htmlID()?>
<?=$field->htmlAttributes()?>
<?=$field->htmlTarget()?>
<?=$field->htmlHREF()?>
<?=$field->htmlRelation()?>><?=$field->htmlIcon()?>
<?php if ($field->hasLabel()) : ?>
<?=$field->renderLabelText()?>
<?php else : ?>
<?=html(isset($field->href)?$field->href:'---')?>
<?php endif; ?>
</a></span>
