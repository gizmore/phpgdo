<?php
namespace GDO\Table\tpl;
use GDO\Table\GDT_ListItem;
/** @var $gdt GDT_ListItem **/
$gdt->addClass('gdt-list-item');
?>
<!-- BEGIN LIST ITEM -->
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

<?php if (isset($gdt->image) ||  isset($gdt->content) || isset($gdt->right)) : ?>
  <div class="gdt-li-main">
<?php if (isset($gdt->image)) : ?>
    <div class="gdt-li-image"><?=$gdt->image->render()?></div>
<?php endif; ?>
<?php if (isset($gdt->content)) : ?>
    <div class="gdt-li-content">
      <?=$gdt->content->renderList()?>
    </div>
<?php endif; ?>
<?php if (isset($gdt->right)) : ?>
    <div class="gdt-li-right"><?=$gdt->right->render()?></div>
<?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($gdt->hasActions() || $gdt->hasFooter()) : ?>
  <div class="gdt-li-lower">
    <div class="gdt-li-footer"><?=$gdt->footer->render()?></div>
    <div class="gdt-li-actions"><?=$gdt->actions()->render()?></div>
  </div>
<?php endif; ?>

</div>
