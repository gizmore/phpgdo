<?php
namespace GDO\UI\tpl;
/** @var $cell bool **/
/** @var $field \GDO\UI\GDT_Tabs **/
?>
<div class="gdt-tabs">
<?php
foreach ($field->getTabs() as $tab) :
  echo $cell ? $tab->renderHTML() : $tab->renderForm();
endforeach;
?>
</div>
