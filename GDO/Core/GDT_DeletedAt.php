<?php
namespace GDO\Core;

use GDO\Date\GDT_Timestamp;

/**
 * Mark a row as deleted.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0
 */
final class GDT_DeletedAt extends GDT_Timestamp
{
	public bool $writeable = false;
	
	public function defaultLabel(): static { return $this->label('deleted_at'); }

}
