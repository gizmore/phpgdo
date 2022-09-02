<?php
namespace GDO\Form\tpl;
/** @var \GDO\Form\GDT_Submit $field **/
?>
<div class="gdt-submit"><?=$field->htmlIcon()?><input
 type="submit"
<?=$field->htmlName()?>
<?=$field->htmlAttributes()?>
<?=$field->htmlDisabled()?>
<?=$field->htmlValue()?> /></div>
