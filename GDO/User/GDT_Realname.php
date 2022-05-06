<?php
namespace GDO\User;

use GDO\Core\GDT_String;

/**
 * A Person's realname. 
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_Realname extends GDT_String
{
	public int $min = 3;
	public int $max = 96;
	public string $icon = 'face';
	public string $pattern = '#\\w[\\w ]+#iD';
	
	public function defaultLabel() { return $this->label('realname'); }
	
}
