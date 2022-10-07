<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Index;
use GDO\Core\ModuleLoader;
use GDO\DB\Result;
use GDO\DB\Query;

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
final class GDO_UserSetting extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->primary()->unique(false),
			GDT_String::make('uset_var'),
		    GDT_Index::make('uset_user_index')->indexColumns('uset_user')->hash(),
		];
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
	
}
