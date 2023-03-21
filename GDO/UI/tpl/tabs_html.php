<?php
namespace GDO\UI\tpl;

use GDO\UI\GDT_Tabs;

/** @var $cell bool * */
/** @var $field GDT_Tabs * */
?>
<div class="gdt-tabs">
	<?php
	foreach ($field->getTabs() as $tab) :
		echo $cell ? $tab->renderHTML() : $tab->renderForm();
	endforeach;
	?>
</div>
