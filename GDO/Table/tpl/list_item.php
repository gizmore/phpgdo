<?php
namespace GDO\Table\tpl;

use GDO\Core\GDT;
use GDO\Table\GDT_ListItem;

/** @var $gdt GDT_ListItem * */
$gdt->addClass('gdt-list-item');
?>
<!-- BEGIN LIST ITEM -->
<div

	<?=$gdt->htmlAttributes()?>>
	<?php
	if ($gdt->hasAvatar() || $gdt->hasTitle() || $gdt->hasSubTitle()) : ?>
        <div class="gdt-li-upper">
			<?php
			if ($gdt->hasAvatar()) : ?>
                <div class="gdt-li-avatar"><?=$gdt->renderAvatar()?></div>
			<?php
			endif; ?>
			<?php
			if ($gdt->hasTitle() || $gdt->hasSubTitle()) : ?>
                <div class="gdt-li-title-texts">
					<?php
					if ($gdt->hasTitle()) : ?>
                        <div class="gdt-li-title"><?=$gdt->renderTitle()?></div>
					<?php
					endif; ?>
					<?php
					if ($gdt->hasSubTitle()) : ?>
                        <div class="gdt-li-subtitle"><?=$gdt->renderSubTitle()?></div>
					<?php
					endif; ?>
                </div>
			<?php
			endif; ?>
        </div>
	<?php
	endif; ?>

	<?php
	if (isset($gdt->image) || $gdt->hasFields() || isset($gdt->content) || isset($gdt->right)) : ?>
        <div class="gdt-li-main">
			<?php
			if (isset($gdt->image)) : ?>
                <div class="gdt-li-image"><?=$gdt->image->render()?></div>
			<?php
			endif; ?>
			<?php
			if (isset($gdt->content) || $gdt->hasFields()) : ?>
                <div class="gdt-li-content">
					<?php
					if (isset($gdt->content)) : ?>
						<?=$gdt->content->renderList()?>
					<?php
					endif; ?>
					<?php
					if ($gdt->hasFields()) : ?>
						<?=$gdt->renderFields(GDT::RENDER_LIST)?>
					<?php
					endif; ?>
                </div>
			<?php
			endif; ?>
			<?php
			if (isset($gdt->right)) : ?>
                <div class="gdt-li-right"><?=$gdt->right->render()?></div>
			<?php
			endif; ?>
        </div>
	<?php
	endif; ?>

	<?php
	if ($gdt->hasActions() || $gdt->hasFooter()) : ?>
        <div class="gdt-li-lower">
			<?php
			if ($gdt->hasFooter()) : ?>
                <div class="gdt-li-footer"><?=$gdt->footer->render()?></div>
			<?php
			endif; ?>
			<?php
			if ($gdt->hasActions()) : ?>
                <div class="gdt-li-actions"><?=$gdt->actions()->render()?></div>
			<?php
			endif; ?>
        </div>
	<?php
	endif; ?>

</div>
