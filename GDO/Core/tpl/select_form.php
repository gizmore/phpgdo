<?php
namespace GDO\Core\tpl;
use GDO\Core\GDT_Select;
/** @var $field GDT_Select **/
if (isset($field->completionHref))
{
    $field->addClass('gdo-autocomplete');
}
?>
<div class="gdt-container <?=$field->classError()?>">
  <?php require 'select_cell.php'; ?>
  <?=$field->htmlError()?>
</div>
