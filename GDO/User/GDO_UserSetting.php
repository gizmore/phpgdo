<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Index;
use GDO\Core\ModuleLoader;
use GDO\DB\Result;

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
	public function gdoDependencies() { return ['GDO\User\GDO_User', 'GDO\Core\GDO_Module']; }
	public function gdoColumns() : array
	{
		return array(
			GDT_User::make('uset_user')->primary(),
			GDT_Name::make('uset_name')->primary()->unique(false),
			GDT_String::make('uset_value'),
		    GDT_Index::make('uset_user_index')->indexColumns('uset_user')->hash(),
		);
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
	public static function usersWith(string $moduleName, string $key, string $var, bool $like=false) : Result
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		$gdt = $module->setting($key);
		$all = $var === $gdt->initial;
		$key = quote($key);
		
		$query = GDO_User::table()->select('gdo_user.*');
		$query->join("LEFT JOIN gdo_usersetting ON user_id=uset_user AND uset_name={$key}");
		
		if ($like)
		{
			$query->where("uset_value LIKE \"{$var}\"");
		}
		else
		{
			$var = quote($var);
			$query->where("uset_value = $var");
		}
		
		if ($all)
		{
			$query->orWhere('uset_value IS NULL');
		}
		
		return $query->fetchTable(GDO_User::table())->exec();
	}
	
}
