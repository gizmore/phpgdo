<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Box;
/** @var GDT_Box $field  **/
?>
<div class="gdt-box <?=$field->flexClass()?>">
<?php foreach ($field->getAllFields() as $gdt) : ?>
  <?=$gdt->render()?>
<?php endforeach; ?>
</div>
