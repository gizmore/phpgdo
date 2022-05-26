<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Form;
use GDO\Core\GDT;
/**
 * @var GDT_Form $field
 */
?>
<div class="gdt-form">
<?php if ($field->hasError()) : ?>
  <?=$field->renderError()?>
<?php endif;?>
 <form method="<?=$field->verb?>">
  <div class="gdt-form-inner">
   <div class="gdt-form-text">
<?php if ($field->hasTitle()) : ?>
    <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
<?php if ($field->hasText()) : ?>
    <p><?=$field->renderText()?></p>
<?php endif; ?>
   </div>
   <div class="gdt-form-fields">
<?php 
$out = '';
$field->withFields(function(GDT $gdt) use (&$out) {
	$out .= $gdt->renderForm();
});
echo $out;
?>
   </div>
<?php if ($field->hasActions()) : ?>
   <div class="gdt-form-actions">
     <?=$field->actions()->renderForm()?>
   </div>
<?php endif; ?>
  </div>
 </form>
</div>
