<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Button;
/** @var $field GDT_Button **/
?>
<div class="gdt-button"<?=$field->htmlAttributes()?>><a
<?=$field->htmlRelation()?>
<?=$field->htmlGDOHREF()?>
<?=$field->htmlDisabled()?>><?=$field->htmlIcon()?> <?=$field->renderLabel()?></a></div>
