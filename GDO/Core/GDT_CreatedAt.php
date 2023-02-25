<?php
namespace GDO\Core;

use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * The created at column is not null and filled upon creation.
 * It can not be edited by a user.
 * It has a default label and the default order is descending.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 5.0
 */
class GDT_CreatedAt extends GDT_Timestamp
{
	public bool $notNull = true;
	public bool $writeable = false;
	
	public function defaultLabel() : self { return $this->label('created_at'); }

	public function isDefaultAsc() : bool { return false; }
	
	public function blankData() : array
	{
	    $var = $this->var !== null ? $this->var : Time::getDate();
		return [$this->name => $var];
	}
	
	public function displayValue($var)
	{
	    return $this->gdo->gdoColumn($var)->renderLabel();
	}
	
	public function htmlClass() : string
	{
		return ' gdt-datetime';
	}

}
