<?php
namespace GDO\User;

use GDO\Core\GDT_UInt;
use GDO\Core\WithGDO;

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
	use WithGDO;
	
	public function defaultLabel() : self { return $this->label('level'); }
	
	public string $icon = 'level';
	public ?string $var = '0';
	public ?string $initial = '0';
	public bool $notNull = true;
	
	public function isSearchable() : bool { return false; }
	
	public function renderCell() : string
	{
		if (isset($this->gdo))
		{
			if (!$this->gdo->gdoIsTable())
			{
				if ($this->gdo instanceof GDO_User)
				{
					return $this->gdo->getLevel();
				}
			}
		}
	    return $this->getVar();
	}
	
}
