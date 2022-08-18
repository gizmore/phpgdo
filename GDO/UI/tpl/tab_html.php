<?php
namespace GDO\UI\tpl;
/** @var $cell bool **/
/** @var $field \GDO\UI\GDT_Tab **/
?>
<div class="gdt-tab">
  <h3><?=$field->renderLabel()?></h3>
  <div class="content">
<?php
foreach ($field->getFields() as $gdt) :
	echo $cell ? $gdt->renderHTML() : $gdt->renderForm();
endforeach;?>
  </div>
</div>
