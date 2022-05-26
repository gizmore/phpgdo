<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;

/**
 * Automatically update 'edited at' on updates.
 * NULL on inserts.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.4.0
 */
final class GDT_EditedAt extends GDT_Timestamp
{
	public bool $writeable = false;
	
	public function defaultLabel() : self { return $this->label('edited_at'); }

	public function isHidden() : bool { return true; }
	public function isOrderDefaultAsc() : bool { return false; }
	
	public function gdoBeforeUpdate(GDO $gdo, Query $query) : void
	{
		$now = Time::getDate();
		$query->set($this->identifier() . "=" . quote($now));
		$this->gdo->setVar($this->name, $now);
	}

	public function htmlClass() : string
	{
		return ' gdt-datetime';
	}
	
}
