<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Button;
/** @var $field GDT_Button **/
?>
<div class="gdt-button"<?=$field->htmlAttributes()?>><?=$field->htmlIcon()?><a
<?=$field->htmlRelation()?>
<?=$field->htmlHREF()?>
<?=$field->htmlDisabled()?>><?=$field->renderLabel()?></a></div>
