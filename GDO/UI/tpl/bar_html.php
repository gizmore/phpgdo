<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Bar;
/** @var GDT_Bar $field  **/
$field->addClass("gdt-bar {$field->flexClass()}");
?>
<div <?=$field->htmlAttributes()?>>
<?php
foreach ($field->getFields() as $gdt)
{
	echo $gdt->render();
}
?>
</div>
