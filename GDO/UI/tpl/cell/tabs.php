<?php /** @var $field \GDO\UI\GDT_Tabs **/ ?>
<div class="gdo-tabs">
<?php
foreach ($field->getTabs() as $tab) :
  echo $cell ? $tab->renderHTML() : $tab->renderForm();
endforeach;
?>
</div>
