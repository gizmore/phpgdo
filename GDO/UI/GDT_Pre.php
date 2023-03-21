<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * An HTML <pre> element.
 *
 * @version 7.0.1
 * @since 6.10.4
 * @author gizmore
 * @see GDT_DIV
 * @see GDT_Tabs
 * @see GDT_Headline
 */
final class GDT_Pre extends GDT
{

	use WithText;

	public function renderCard(): string
	{
		return $this->renderHTML();
	}

	public function renderHTML(): string
	{
		return sprintf('<div class="gdt-pre">%s</div>', $this->renderText());
	}

	public function renderJSON()
	{
		return $this->renderText();
	}

	public function renderCLI(): string
	{
		return $this->renderText() . "\n";
	}

}
