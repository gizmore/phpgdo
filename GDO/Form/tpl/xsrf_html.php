<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_AntiCSRF;
/** @var $field GDT_AntiCSRF **/
?>
<div class="gdt-container<?=$field->classError()?>">
  <input
   type="hidden"
   <?=$field->htmlFormName()?>
   value="<?=$field->csrfToken()?>" />
  <?=$field->htmlError()?>
</div>
