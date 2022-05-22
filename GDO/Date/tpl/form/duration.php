<?php /** @var $field \GDO\Date\GDT_Duration **/ ?>
<div class="gdt-container<?= $field->classError(); ?>">
  <?= $field->htmlIcon(); ?>
  <label <?=$field->htmlForID()?>><?= $field->renderLabel(); ?></label>
  <input
   <?=$field->htmlID()?>
   type="text"
   <?=$field->htmlFormName()?>
   <?= $field->htmlDisabled(); ?>
   <?= $field->htmlRequired(); ?>
   value="<?= $field->getVar(); ?>" />
  <?= $field->htmlError(); ?>
</div>
