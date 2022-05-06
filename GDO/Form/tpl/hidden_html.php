<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Hidden;
/**
 * @var GDT_Hidden $field
 */
?>
<input type="hidden"
 name="<?=$field->htmlName()?>"
 value="<?=$field->htmlVar()?>" />
