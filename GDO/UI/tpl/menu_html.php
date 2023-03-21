<?php
namespace GDO\UI\tpl;

use GDO\UI\GDT_Menu;

/** @var $field GDT_Menu * */
$field->addClass('gdt-menu');
?>
<div<?=$field->htmlAttributes()?>>
	<?php
	if ($field->hasLabel()) : ?>
        <div class="menu-title"><?=$field->renderLabel()?></div>
	<?php
	endif; ?>
	<?php
	foreach ($field->getFields() as $gdt) : ?>
		<?=$gdt->render()?>
	<?php
	endforeach; ?>
</div>
