<?php
namespace GDO\UI\tpl;

use GDO\UI\GDT_Image;

/** @var $field GDT_Image * */
?>
<span class="gdt-image"><img
<?=$field->htmlID()?>
		<?=$field->htmlName()?>
		<?=$field->htmlSrc()?>
		<?=$field->htmlAttributes()?> /></span>
