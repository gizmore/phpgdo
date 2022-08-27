<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;

/**
 * A small icon with a badge counter number.
 * Not DB driven.
 * 
 * @author gizmore
 * @since 7.0.1
 * @version 6.10.0
 */
class GDT_Badge extends GDT
{
	use WithIcon;
	use WithLabel;
	use WithPHPJQuery;
	
	protected function __construct()
	{
		parent::__construct();
		$this->icon('badge');
	}
	
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
