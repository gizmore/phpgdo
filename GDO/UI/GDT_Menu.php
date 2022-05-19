<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\Core\GDT_Template;

/**
 * A popup menu
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.4.0
 */
final class GDT_Menu extends GDT
{
	use WithLabel;
	use WithFields;
	
	public function defaultName() { return 'menu'; }
	
	public function renderCell() : string { return GDT_Template::php('UI', 'cell/menu.php', ['field'=>$this]); }
	
}
