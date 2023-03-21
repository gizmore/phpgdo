<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithValue;

/**
 * Very simple field that only has custom html content.
 *
 * @version 7.0.1
 * @since 6.7.0
 * @author gizmore
 */
final class GDT_HTML extends GDT
{

	use WithValue;

	public function renderHTML(): string
	{
		return isset($this->var) ? $this->var : GDT::EMPTY_STRING;
	}

	public function renderCard(): string
	{
		return '<div class="gdt-html">' . $this->var . '</div>'; # Not getVar() to prevent XSS.
	}

	/**
	 * UnitTest default value.
	 */
	public function plugVars(): array
	{
		return [
			[$this->getName() => '<strike>HTML</strike>'],
		];
	}

}
