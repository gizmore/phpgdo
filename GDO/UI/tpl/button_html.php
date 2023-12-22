<?php
namespace GDO\UI\tpl;

use GDO\Core\GDT;
use GDO\UI\GDT_Button;

/** @var $cell bool * */
/** @var $field GDT_Button * */
$label = $cell ? GDT::EMPTY_STRING : $field->renderLabelText();
if (!($href = $field->htmlGDOHREF()))
{
	$field->addClass('gdo-disabled');
}
$field->addClass('gdt-button');

?>
<div<?=$field->htmlAttributes()?>><a
		<?=$field->htmlRelation()?>
		<?=$href?>
		<?=$field->htmlDisabled()?>><?=$field->htmlIcon()?><?=$label?></a></div>
