<?php
namespace GDO\Core;
use GDO\DB\Database;

/**
 * The auto inc column is unsigned and sets the primary key after insertions.
 *
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see GDT_CreatedAt
 * @see GDT_CreatedBy
 * @see GDT_EditedAt
 * @see GDT_EditedBy
 */
final class GDT_AutoInc extends GDT_UInt
{
	############
	### Base ###
	############
	public bool $notNull = true;
	public bool $writable = false;

	public function defaultLabel() { return $this->label('id'); }
	
	##############
	### Column ###
	##############
	public function gdoColumnNames() { return [$this->name]; }
	public function primary($primary=true) { return $this; } 
	public function isPrimary() { return true; } # Weird workaround for mysql primary key defs.
	public function gdoColumnDefine() : string { return "{$this->identifier()} {$this->gdoSizeDefine()}INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY"; }
	public function validate($value) : bool { return true; } # We simply do nothing in the almighty validate.
	
	##############
	### Events ###
	##############
	public function gdoAfterCreate()
	{
		if ($id = Database::$INSTANCE->insertId())
		{
			$this->gdo->setVar($this->name, $id, false);
		}
	}
	
	public function blankData()
	{
		# prevent old values to be used.
	    return [$this->name => null];
	}
	
}
