<?php
namespace GDO\Form\tpl;
use GDO\UI\GDT_Error;
/** @var \GDO\Form\GDT_Form $field **/
?>
<?php if ($field->hasError()) : ?>
<?=GDT_Error::make()->textRaw($field->renderError())->render()?>
<?php endif;?>
<div<?=$field->htmlID()?><?=$field->htmlAttributes()?>>
<form<?=$field->htmlVerb()?><?=$field->htmlAction()?><?=$field->htmlTarget()?>>
 <div class="gdt-form-inner">
<?php if ($field->hasTitle() || $field->hasText()) : ?>
   <div class="gdt-form-text">
<?php if ($field->hasTitle()) : ?>
    <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
<?php if ($field->hasText()) : ?>
    <p><?=$field->renderText()?></p>
<?php endif; ?>
   </div>
<?php endif; ?>
<?php if ($field->hasFields()) : ?>
   <div class="gdt-form-fields">
<?php 
foreach ($field->getFields() as $gdt)
{
	if (isset($field->gdo))
	{
		$gdt->gdo($field->gdo);
	}
	echo $gdt->renderForm();
}?></div>
<?php endif; ?>
<?php if ($field->hasActions()) : ?>
   <div class="gdt-form-actions">
<?=$field->actions()->renderForm()?>
  </div>
<?php endif; ?>
 </div>
</form>
</div>
