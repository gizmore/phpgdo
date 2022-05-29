<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Panel;
/**
 * @var $field GDT_Panel
 */
?>
<div class="gdt-panel"<?=$field->htmlAttributes()?>>
  <p><?=$field->renderText()?></p>
</div>
