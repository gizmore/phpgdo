<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\User\GDT_User;
use GDO\User\GDO_User;

/**
 * Automatically updates the editor user on update queries.
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
final class GDT_EditedBy extends GDT_User
{
	public bool $hidden = true;
	public bool $writeable = false;
	
	public function defaultLabel(): static { return $this->label('edited_by'); }
	
	public function gdoBeforeUpdate(GDO $gdo, Query $query) : void
	{
  		$userId = GDO_User::current()->getID();
   		$userId = $userId > 0 ? $userId : 1;
   		$query->set($this->identifier() . '=' . $userId);
   		$this->gdo->setVar($this->name, $userId);
	}

}
