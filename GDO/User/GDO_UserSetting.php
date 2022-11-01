<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Index;
use GDO\Core\ModuleLoader;
use GDO\DB\Result;
use GDO\DB\Query;
use GDO\Core\GDT;

/**
 * Similiar to modulevars, this table is for user vars.
 * 
 * @hook UserSettingChange(GDO_User, key, var)
 * 
 * @author gizmore@wechall.net
 * @version 7.0.1
 * @since 6.0.0
 * @see GDO_Module for user settings API.
 */
class GDO_UserSetting extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->caseS()->primary()->unique(false),
			GDT_String::make('uset_var'),
			GDT_ACLRelation::make('uset_relation'),
			GDT_Level::make('uset_level'),
			GDT_Permission::make('uset_permission'),
			GDT_Index::make('uset_user_index')->indexColumns('uset_user,uset_name')->hash(),
		];
	}
	
	public function getRelation(): ?string
	{
		return $this->gdoVar('uset_relation');
	}
	
	public function getLevel(): int
	{
		return $this->gdoVar('uset_level');
	}
	
	public function getPermission(): ?string
	{
		return $this->gdoVar('uset_permission');
	}
	
	################
	### ACL Data ###
	################
	public function toACLData(): array
	{
		return [
			$this->getRelation(),
			$this->getLevel(),
			$this->getPermission(),
		];
	}

	public static function updateACL(GDO_User $user, GDT $gdt, string $aclField, ?string $aclVar): void
	{
		foreach ($gdt->getGDOData() as $key => $var)
		{
			if ($row = self::table()->getById($user->getID(), $key))
			{
				$row->saveVar($aclField, $aclVar);
			}
		}
	}
	
	##############
	### Static ###
	##############
	/**
	 * Get all users with a specified setting like....
	 * Return as DB result.
	 */
	public static function usersWithLike(string $moduleName, string $key, string $var) : Result
	{
		return self::usersWithLike($moduleName, $key, $var, true);
	}
	
	/**
	 * Get all users with a specified setting.
	 * Return as DB result.
	 */
	public static function usersWith(string $moduleName, string $key, string $var, string $op='=') : Result
	{
		return self::usersWithQuery($moduleName, $key, $var, $op)->exec();
	}
	
	public static function usersWithQuery(string $moduleName, string $key, ?string $var, string $op='=') : Query
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		$gdt = $module->setting($key);
		$key = quote($key);
		$all = $var === $gdt->initial;
		
		$query = GDO_User::table()->select('gdo_user.*');
		$query->join("LEFT JOIN gdo_usersetting ON user_id=uset_user AND uset_name={$key}");
		
		$op = strtoupper($op);
		if ($op === 'LIKE')
		{
			$query->where("uset_var LIKE \"{$var}\"");
		}
		else
		{
			$op = $var === null ? 'IS' : $op;
			$var = quote($var);
			$query->where("uset_var $op $var");
		}
		
		if ($all)
		{
			$query->orWhere('uset_var IS NULL');
		}
		
		return $query->fetchTable(GDO_User::table());
	}
	
	## 
	
	/**
	 * Decorate a query with acl relation query.
	 */
	public static function whereSettingVisible(Query $query, string $moduleName, string $key, GDO_User $user, string $userFieldName='gdo_user.user_id'): Query
	{
// 		$user = GDO_User::current();
		$module = GDO_Module::getByName($moduleName);
		$settingACL = $module->getSettingACL($key);
		$settingACL->queryWhereVisible($query, $moduleName, $key, $user);
// 		$settingRel = $settingACL->aclRelation;
// 		$settingRel->aclQuery($query, $user, $userFieldName);
		return $query;
// 		$validACLVars = [];
// 		$defaultACL = 
// 		$aclField = "_acl_{$key}_relation";
// 		$query->select("(SELECT uset_var FROM gdo_usersetting WHERE uset_name={$aclField} AND uset_var IN ($validACLVars) ) ");
// 		$query->where("( SELECT 1 FROM gdo_usersetting ust WHERE ust.uset_user={$userFieldName} AND _acl_favorite_religion_relation )")
// 		return $query;
	}
	
}
