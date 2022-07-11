<?php
/** @var $field \GDO\UI\GDT_Card **/
$field->addClass('gdt-card');
?>
<div
<?php if (isset($field->gdo)) : ?>
  id="card-<?=$field->gdo->getID()?>"
<?php endif; ?>
  <?=$field->htmlAttributes()?>>

<?php if ($field->avatar || $field->hasTitle() || $field->hasSubTitle()) : ?>
  <div class="gdt-card-upper">
<?php if ($field->avatar) : ?>
    <div class="gdt-card-avatar"><?=$field->avatar->renderCell()?></div>
<?php endif; ?>
<?php if ($field->hasTitle() || $field->hasSubTitle()) : ?>
    <div class="gdt-card-title-texts">
<?php if ($field->hasTitle()) : ?>
    <div class="gdt-card-title"><?=$field->renderTitle()?></div>
<?php endif; ?>
<?php if ($field->hasSubTitle()) : ?>
    <div class="gdt-card-subtitle"><?=$field->renderSubTitle()?></div>
<?php endif; ?>
    </div>
<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($field->image || $field->content || $field->fields) : ?>
  <div class="gdt-card-middle">
<?php if ($field->image) : ?>
    <div class="gdt-card-image"><?=$field->image->renderCard()?></div>
<?php endif; ?>
<?php if ($field->content) : ?>
    <div class="gdt-card-content"><?=$field->content->renderCard()?></div>
<?php endif; ?>
<?php if ($field->fields) : ?>
    <div class="gdt-card-fields">
    <?php foreach ($field->fields as $gdt) : ?>
      <?=$gdt->renderCard()?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($field->footer || $field->actions) : ?>
  <div class="gdt-card-lower">
<?php if ($field->footer) : ?>
    <div class="gdt-card-footer"><?=$field->footer->renderCell()?></div>
<?php endif; ?>
<?php if ($field->actions) : ?>
    <div class="gdt-card-actions"><?=$field->actions()->renderCell()?></div>
<?php endif; ?>
  </div>
<?php endif; ?>
  
</div>
