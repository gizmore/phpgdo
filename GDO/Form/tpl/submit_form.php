<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Submit;
/** @var GDT_Submit $field **/
?>
<div class="gdt-submit">
  <?=$field->htmlIcon()?>
  <input
   type="submit"
   <?=$field->htmlFormName()?>
   <?=$field->htmlAttributes()?>
   <?=$field->htmlDisabled()?>
   <?=$field->htmlValue()?> />
</div>
