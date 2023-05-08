<?php
namespace GDO\UI\tpl;

use GDO\UI\GDT_SVGImage;

/** @var $field GDT_SVGImage * */
?>
<span class="gdt-image gdt-svg-image"
<?=$field->htmlAttributes()?>
><object data="<?=$field->src?>"
		 type="image/svg+xml"></object></span>
