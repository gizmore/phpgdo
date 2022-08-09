<?php
namespace GDO\UI\tpl;
/**
 * @var $field \GDO\UI\GDT_Panel
 */
?>
<div<?=$field->htmlAttributes()?>>
<?php if ($field->hasTitle()) : ?>
<h3><?=$field->htmlIcon()?><?=$field->renderTitle()?></h3>
<?php endif; ?>
<p><?=$field->renderText()?></p>
</div>
