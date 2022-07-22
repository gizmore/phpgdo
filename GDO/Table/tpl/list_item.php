<?php
namespace GDO\Table\tpl;
use GDO\Table\GDT_ListItem;
/** @var $gdt GDT_ListItem **/
$gdt->addClass('gdt-list-item');
?>
<div
 <?=$gdt->htmlAttributes()?>>
<?php if ($gdt->hasAvatar() || $gdt->hasTitle() || $gdt->hasSubTitle()) : ?>
  <div class="gdt-li-upper">
<?php if ($gdt->hasAvatar()) : ?>
	<div class="gdt-li-avatar"><?=$gdt->renderAvatar()?></div>
<?php endif; ?>
<?php if ($gdt->hasTitle() || $gdt->hasSubTitle()) : ?>
    <div class="gdt-li-title-texts">
<?php if ($gdt->hasTitle()) : ?>
      <div class="gdt-li-title"><?=$gdt->renderTitle()?></div>
<?php endif; ?>
<?php if ($gdt->hasSubTitle()) : ?>
      <div class="gdt-li-subtitle"><?=$gdt->renderSubTitle()?></div>
<?php endif; ?>
    </div>
<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($gdt->image ||  $gdt->content || $gdt->right) : ?>
  <div class="gdt-li-main">
<?php if ($gdt->image) : ?>
    <div class="gdt-li-image"><?=$gdt->image->renderCell()?></div>
<?php endif; ?>
<?php if ($gdt->content) : ?>
    <div class="gdt-li-content">
      <?=$gdt->content->render()?>
    </div>
<?php endif; ?>
<?php if ($gdt->right) : ?>
    <div class="gdt-li-right"><?=$gdt->right->renderCell()?></div>
<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($gdt->hasActions()) : ?>
  <div class="gdt-li-lower">
<?php if ($gdt->hasActions()) : ?>
    <div class="gdt-li-actions"><?=$gdt->actions()->renderCell()?></div>
<?php endif; ?>
  </div>
<?php endif; ?>
</div>
