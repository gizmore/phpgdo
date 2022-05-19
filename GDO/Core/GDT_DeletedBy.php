<?php
namespace GDO\Core;

use GDO\User\GDT_User;

/**
 * GDT signals deletion for a row. Some stuff auto-detects that.
 * Not often used yet.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.11.2
 */
final class GDT_DeletedBy extends GDT_User
{
	public bool $writable = false;
	public function isHidden() : bool { return true; }
	public function defaultLabel() : self { return $this->label('deleted_by'); }

}
