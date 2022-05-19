<?php
namespace GDO\User;

use GDO\Core\GDT_UInt;

/**
 * User level field.
 * 
 * If the gdo is a user, it reads combined level of user permission.
 * NotNull, initial 0, because we want to do arithmetics.
 * With trophy icon.
 * Renders effective level in table cells.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.2
 */
final class GDT_Level extends GDT_UInt
{
	public function defaultLabel() : self { return $this->label('level'); }
	
	public $icon = 'level';
	public ?string $var = '0';
	public ?string $initial = '0';
	public bool $notNull = true;
	
	public function isSearchable() : bool { return false; }
	
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
