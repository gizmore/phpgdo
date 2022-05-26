<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Form;
use GDO\UI\GDT_Error;
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
      <?php foreach ($field->getFields() as $gdt) : ?>
        <?=$gdt->renderForm()?>
      <?php endforeach; ?>
   </div>
<?php if ($field->hasActions()) : ?>
   <div class="gdt-form-actions">
    <?php foreach ($field->actions()->getFields() as $gdt) : ?>
      <?=$gdt->renderForm()?>
    <?php endforeach; ?>
   </div>
<?php endif; ?>
  </div>
 </form>
</div>
