<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Form;
/**
 * @var GDT_Form $field
 */
?>
<div class="gdt-form">
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
