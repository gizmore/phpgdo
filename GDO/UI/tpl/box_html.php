<?php
namespace GDO\UI\tpl;
use GDO\UI\GDT_Box;
/** @var GDT_Box $field  **/
?>
<div class="gdt-box">
<?php foreach ($field->getFields() as $gdt) : ?>
  <?=$gdt->render()?>
<?php endforeach; ?>
</div>
