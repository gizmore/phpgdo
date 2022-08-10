<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * Has text.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Paragraph extends GDT
{
    use WithText;

    ##############
    ### Render ###
    ##############
    public function render() : string { return $this->renderHTML(); }
	public function renderCLI() : string { return $this->renderText() . "\n"; }
    public function renderHTML() : string { return sprintf("<p class=\"gdt-paragraph\">%s</p>\n", $this->renderText()); }
    public function renderJSON() { return $this->renderText(); }

}
