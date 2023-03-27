<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * A simple paragraph.
 * Has text.
 *
 * @version 7.0.1
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Paragraph extends GDT
{

	use WithText;

	##############
	### Render ###
	##############
	public function render(): array|string|null { return $this->renderHTML(); }

	public function renderHTML(): string { return sprintf("<p class=\"gdt-paragraph\">%s</p>\n", $this->renderText()); }

	public function renderCLI(): string { return $this->renderText() . "\n"; }

	public function renderJSON(): array|string|null { return $this->renderText(); }

}
