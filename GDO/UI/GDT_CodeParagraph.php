<?php
namespace GDO\UI;

/**
 * A simple code paragraph.
 * No template for speedup and less templates?
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.11.0
 */
class GDT_CodeParagraph extends GDT_Paragraph
{
	public function renderHTML() : string
	{
		return "<div class=\"gdt-code\"><code><pre>{$this->renderText()}\n</pre></code></div>\n";
	}

}
