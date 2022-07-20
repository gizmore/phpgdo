<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Hidden;
/** @var GDT_Hidden $field **/
?>
<input type="hidden"
 <?=$field->htmlName()?>
 <?=$field->htmlValue()?> />
