<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A small icon with a badge counter number.
 * 
 * @author gizmore
 * @since 7.0.1
 * @version 6.10.0
 */
class GDT_Badge extends GDT
{
	use WithLabel;
	use WithPHPJQuery;
	
	#############
	### Badge ###
	#############
	public int $badge;
	public function badge(int $badge) :self
	{
		$this->badge = $badge;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return GDT_Template::php('UI', 'cell/badge.php', ['field' => $this]);
	}
	
}
