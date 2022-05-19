<?php
namespace GDO\User;

use GDO\UI\GDT_IconButton;
use GDO\Core\GDT_Template;

/**
 * Show a trophy with level badge.
 * A tooltip explains if your access is granted or restricted.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.12.1
 */
final class GDT_LevelPopup extends GDT_IconButton
{
	public int $level = 0;
	public function level(int $level)
	{
		$this->level = $level;
		return $this;
	}
	
	public function renderCell() : string
	{
		return GDT_Template::php('User', 'cell/levelpopup.php', ['field'=>$this]);
	}
	
}
