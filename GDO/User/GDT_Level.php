<?php
namespace GDO\User;

use GDO\Core\GDT_UInt;

/**
 * User level field.
 * If the gdo is a user, it reads combined level of user permission.
 * NotNull, initial 0, because we want to do arithmetics.
 * With trophy icon.
 * Renders effective level in table cells.
 * 
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.2
 */
final class GDT_Level extends GDT_UInt
{
	public function defaultLabel() { return $this->label('level'); }
	
	public $icon = 'level';
	public $var = '0';
	public $initial = '0';
	public $nullable = false;
	
	public function isSearchable() { return false; }
	
	public function renderCell() : string
	{
	    if ( ($user = $this->gdo) &&
	         (!$user->isTable()) &&
	         ($user instanceof GDO_User)
	       ) 
	    {
            return $user->getLevel();
	    }
	    return $this->var;
	}
	
}
