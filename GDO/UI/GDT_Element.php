<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Baseclass for various HTML elements.
 */
abstract class GDT_Element extends GDT
{
	use WithText;
	use WithPHPJQuery;

	abstract protected function tagName(): string;

	public function renderCLI(): string
	{
		return $this->renderText();
	}

	public function renderHTML(): string
	{
		$tag = $this->tagName();
		$txt = $this->renderText();
		$atr = $this->htmlAttributes()
		return "<$tag $atr>$txt</$tag>";
	}

}
