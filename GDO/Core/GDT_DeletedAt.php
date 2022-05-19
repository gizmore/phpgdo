<?php
namespace GDO\Core;

use GDO\Date\GDT_Timestamp;

/**
 * Mark a row as deleted.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 5.0
 */
final class GDT_DeletedAt extends GDT_Timestamp
{
	public function isWritable() : bool { return false; }
	
	public function defaultLabel() : self { return $this->label('deleted_at'); }

}
