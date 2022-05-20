<?php
namespace GDO\User;

use GDO\Core\GDT_Template;
use GDO\UI\GDT_Button;

/**
 * Show a trophy with level badge.
 * A tooltip explains if your access is granted or restricted.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.12.1
 */
final class GDT_LevelPopup extends GDT_Button
{
	public int $level = 0;
	public function level(int $level) : self
	{
		$this->level = $level;
		return $this;
	}
	
	public function renderHTML() : string
	{
		return GDT_Template::php('User', 'cell/levelpopup.php', ['field'=>$this]);
	}
	
}
