<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Form;
/**
 * @var GDT_Form $field
 */
?>
<div class="gdt-form<?=$field->slim?' gdt-form-slim':' gdt-form-compact'?>" <?=$field->htmlID()?>>

<?php if ($field->hasError()) : ?>
  <?=$field->renderError()?>
<?php endif;?>

 <form<?=$field->htmlVerb()?><?=$field->htmlAction()?>>
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
	echo $gdt->renderForm();
}
?>
   </div>
<?php endif; ?>

<?php if ($field->hasActions()) : ?>
   <div class="gdt-form-actions">
     <?=$field->actions()->renderForm()?>
   </div>
<?php endif; ?>

  </div>
 </form>
</div>
