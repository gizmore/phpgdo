<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Bar;
/** @var GDT_Bar $field  **/
?>
<div class="gdt-bar <?=$field->flexClass()?>">
<?php
foreach ($field->getFields() as $gdt)
{
	echo $gdt->render();
}
?>
</div>
