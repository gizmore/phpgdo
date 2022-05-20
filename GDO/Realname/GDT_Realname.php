<?php
namespace GDO\Realname;

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
	public $icon = 'face';
	public string $pattern = '#\\w[\\w ]+#iD';
	
	public function defaultLabel() : self { return $this->label('realname'); }
	
}
