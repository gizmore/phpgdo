<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Index;

/**
 * Table for user<=>permission relation.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @see GDO_Permission
 */
final class GDO_UserPermission extends GDO
{

	/**
	 * Fetch all user permissions.
	 *
	 * @return string[]
	 */
	public static function load(GDO_User $user): array
	{
		if (!$user->isPersisted())
		{
			return GDT::EMPTY_ARRAY;
		}
		return self::table()->select('perm_name')->
		joinObject('perm_perm_id')->
		where("perm_user_id={$user->getID()}")->
		exec()->fetchColumn();
	}

	/**
	 * Grant via name.
	 */
	public static function grant(GDO_User $user, string $permission): void
	{
		self::grantPermission($user, GDO_Permission::getByName($permission));
	}

	/**
	 * Grant via permission object.
	 */
	public static function grantPermission(GDO_User $user, GDO_Permission $permission): void
	{
		if (!$user->hasPermissionObject($permission))
		{
			self::blank([
				'perm_user_id' => $user->getID(),
				'perm_perm_id' => $permission->getID(),
			])->replace();
			GDT_Hook::callHook('UserPermissionGranted', $user, $permission);
		}
	}

	public static function revoke(GDO_User $user, string $permission): void
	{
		self::revokePermission($user, GDO_Permission::getByName($permission));
	}

	public static function revokePermission(GDO_User $user, GDO_Permission $permission): void
	{
		self::table()->deleteWhere("perm_user_id={$user->getID()} AND perm_perm_id={$permission->getID()}");
	}

	public function gdoCached(): bool { return false; }

	##############
	### Static ###
	##############

	public function gdoColumns(): array
	{
		return [
			GDT_User::make('perm_user_id')->primary()->cascade()->withCompletion(),
			GDT_Permission::make('perm_perm_id')->primary()->cascadeRestrict(),
			GDT_CreatedAt::make('perm_created_at'),
			GDT_CreatedBy::make('perm_created_by'),
			GDT_Index::make('perm_user_id_index')->hash()->indexColumns('perm_user_id'),
		];
	}

	public function getUser(): GDO_User { return $this->gdoValue('perm_user_id'); }

	public function getUserID(): string { return $this->gdoVar('perm_user_id'); }

	public function getPermission(): GDO_Permission { return $this->gdoValue('perm_perm_id'); }

	public function getPermissionID(): string { return $this->gdoVar('perm_perm_id'); }

}
