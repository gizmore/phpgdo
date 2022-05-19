<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * Should render well everywhere as a paragraph might be important text.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Paragraph extends GDT
{
    use WithText;

    public function render() : string { return $this->renderCell(); }
	public function renderCLI() : string { return $this->renderText() . "\n"; }
    public function renderCard() : string { return $this->renderCell(); }
    public function renderCell() : string { return sprintf('<p class="gdt-paragraph">%s</p>', $this->renderText()); }
	public function renderForm() : string { return $this->renderCell(); }
	public function renderJSON() { return $this->renderCLI(); }

}
