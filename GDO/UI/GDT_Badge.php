<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_UInt;

/**
 * Like an interger, but with a small badge counter symbol.
 * A small icon with a badge counter number.
 * Not DB driven.
 *
 * @version 6.10.0
 * @since 7.0.1
 * @author gizmore
 */
class GDT_Badge extends GDT_UInt
{

	use WithText;

	public bool $writeable = false;
	public ?string $var = '0';
	public ?string $initial = '0';

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('UI', 'badge_html.php', ['field' => $this]);
	}

// 	public function renderCell(): string
// 	{
// 		GDT_Template::php('UI', 'badge_html.php', ['field' => $this]);
// 	}

	public function renderCard(): string
	{
		return $this->displayCard("{$this->renderText()}&nbsp;<i class=\"gdt-badge\">{$this->var}</i>");
	}

}
