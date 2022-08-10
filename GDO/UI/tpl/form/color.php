<?php /** @var $field \GDO\UI\GDT_Color **/ ?>
<div class="gdt-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label <?=$field->htmlForID()?>><?= $field->renderLabel(); ?></label>
  <input
   type="color"
   <?=$field->htmlID()?>
   <?=$field->htmlName()?>
   value="<?= html($field->getVar()); ?>"
   <?= $field->htmlRequired(); ?>
   <?= $field->htmlDisabled(); ?>/>
  <?= $field->htmlError(); ?>
</div>
