<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Simple html DIV element.
 *
 * @author gizmore
 * @deprecated Unused?
 */
final class GDT_DIV extends GDT
{

	use WithText;
	use WithPHPJQuery;

	public function renderCLI(): string
	{
		return $this->renderText();
	}

	public function renderHTML(): string
	{
		return sprintf('<div%s>%s</div>',
			$this->htmlAttributes(), $this->renderText());
	}

	public function renderForm(): string
	{
		return $this->renderHTML();
	}

	public function renderCard(): string
	{
		return $this->renderHTML();
	}

}
