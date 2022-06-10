<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Panel;
/**
 * @var $field GDT_Panel
 */
?>
<div<?=$field->htmlAttributes()?>>
<?php if ($field->hasTitle()) : ?>
  <h3><?=$field->renderTitle()?></h3>
<?php endif; ?>
  <p><?=$field->htmlIcon()?>
  <?=$field->renderText()?></p>
</div>
