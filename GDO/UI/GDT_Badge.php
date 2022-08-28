<?php
namespace GDO\UI;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_UInt;

/**
 * Like an interger, but with a small badge counter symbol.
 * A small icon with a badge counter number.
 * Not DB driven.
 * 
 * @author gizmore
 * @since 7.0.1
 * @version 6.10.0
 */
class GDT_Badge extends GDT_UInt
{
	public bool $writeable = false;
	public ?string $initial = '0';
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'badge_html.php', ['field' => $this]);
	}
	
	public function renderCard() : string
	{
		return $this->displayCard("<i class=\"gdt-badge\">{$this->var}</i>");
	}
	
}
