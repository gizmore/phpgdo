<?php
namespace GDO\User;

use GDO\Core\WithGDO;
use GDO\UI\GDT_Badge;

/**
 * User level field.
 * 
 * If the gdo is a user, it reads combined level of user permission.
 * NotNull, initial 0, because we want to do arithmetics.
 * With trophy icon.
 * Renders effective level in table cells.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.2
 */
final class GDT_Level extends GDT_Badge
{
	use WithGDO;
	
	public function defaultLabel() : self { return $this->label('user_level'); }
	
	public string $icon = 'level';
	public bool $notNull = true;
	
	public function isSearchable() : bool { return false; }
	
	public function getVar()
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
		return parent::getVar();
	}
	
// 	public function renderHTML() : string
// 	{
// 	}
	
}
