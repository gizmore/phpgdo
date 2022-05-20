<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Paragraph extends GDT
{
    use WithText;

    ##############
    ### Render ###
    ##############
    public function render() : string { return $this->renderCell(); }
	public function renderCLI() : string { return $this->renderText() . "\n"; }
    public function renderHTML() : string { return sprintf("<p class=\"gdt-paragraph\">%s</p>\n", $this->renderText()); }
    public function renderJSON() { return $this->renderText(); }

}
