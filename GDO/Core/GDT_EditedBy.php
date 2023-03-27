<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Query;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * Automatically updates the editor user on update queries.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
final class GDT_EditedBy extends GDT_User
{

	public bool $hidden = true;

	public bool $writeable = false;


	public function defaultLabel(): self
	{
		return $this->label('edited_by');
	}


	public function gdoBeforeUpdate(GDO $gdo, Query $query): void
	{
		$userId = GDO_User::current()->getID() ?: GDT::ONE;
		$query->set("{$this->name}={$userId}");
		$this->gdo->setVar($this->name, $userId);
	}

}
