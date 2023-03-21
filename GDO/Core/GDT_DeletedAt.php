<?php
namespace GDO\Core;

use GDO\Date\GDT_Timestamp;

/**
 * Mark a row as deleted.
 *
 * @version 7.0.1
 * @since 5.0
 * @author gizmore
 */
final class GDT_DeletedAt extends GDT_Timestamp
{

	public bool $writeable = false;

	public function defaultLabel(): self { return $this->label('deleted_at'); }

}
