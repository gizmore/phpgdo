<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Index;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT;

/**
 * Table for user<=>permission relation.
 * 
 * @see GDO_Permission
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 * @see GDO_Permission
 */
final class GDO_UserPermission extends GDO
{
	public function gdoCached() : bool { return false; }
	
	public function gdoColumns() : array
	{
		return [
			GDT_User::make('perm_user_id')->primary()->cascade(),
			GDT_Permission::make('perm_perm_id')->primary()->cascadeRestrict(),
			GDT_CreatedAt::make('perm_created_at'),
			GDT_CreatedBy::make('perm_created_by'),
		    GDT_Index::make('perm_user_id_index')->hash()->indexColumns('perm_user_id'),
		];
	}
	
	public function getUser() : GDO_User { return $this->gdoValue('perm_user_id'); }
	public function getUserID() : string { return $this->gdoVar('perm_user_id'); }
	
	public function getPermission() : GDO_Permission { return $this->gdoValue('perm_perm_id'); }
	public function getPermissionID() : string { return $this->gdoVar('perm_perm_id'); }

	##############
	### Static ###
	##############
	public static function load(GDO_User $user) : array
	{
		if (!$user->isPersisted())
		{
			return GDT::EMPTY_ARRAY;
		}
		return self::table()->select('perm_name, perm_level')->
			joinObject('perm_perm_id')->
			where("perm_user_id={$user->getID()}")->
            exec()->fetchAllArray2dPair();
	}
	
	/**
	 * Grant via permission object.
	 * @param GDO_User $user
	 * @param GDO_Permission $permission
	 * @return static
	 */
	public static function grantPermission(GDO_User $user, GDO_Permission $permission)
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
	
	/**
	 * Grant via name.
	 */
	public static function grant(GDO_User $user, string $permission)
	{
		return self::grantPermission($user, GDO_Permission::getByName($permission));
	}
	
	public static function revokePermission(GDO_User $user, GDO_Permission $permission)
	{
		return self::table()->deleteWhere("perm_user_id={$user->getID()} AND perm_perm_id={$permission->getID()}");
	}
	
	public static function revoke(GDO_User $user, string $permission)
	{
		return self::revokePermission($user, GDO_Permission::getByName($permission));
	}
	
}
