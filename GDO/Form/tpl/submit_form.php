<?php
namespace GDO\Form\tpl;
use GDO\Form\GDT_Submit;
/**
 * Classic implementation of submit button rendering.
 * @var GDT_Submit $field
 */
?>
<input
 type="submit"
 name="<?=$field->htmlName()?>"
 value="<?=$field->htmlVar()?>" />
