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
 * @version 7.0.0
 * @since 5.0
 */
class GDT_CreatedAt extends GDT_Timestamp
{
	public bool $notNull = true;
	public bool $writable = false;
	
	public function defaultLabel() : self { return $this->label('created_at'); }

	public function isOrderDefaultAsc() : bool { return false; }
	
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} TIMESTAMP({$this->millis}){$this->gdoNullDefine()} DEFAULT CURRENT_TIMESTAMP({$this->millis})";
	}
	
	/**
	 * Fill with creation date timestamp.
	 * @see \GDO\Core\GDT::blankData()
	 */
	public function blankData() : array
	{
	    $var = $this->var ? $this->var : Time::getDate();
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
