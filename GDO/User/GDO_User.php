<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Crypto\GDT_PasswordHash;

/**
 * The holy user class.
 * Has temp data.
 * Most user related fields are in other module settings.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 1.0.0
 */
final class GDO_User extends GDO
{
	###############
	### Factory ###
	###############
	public static function getByName(string $name) : ?self
	{
		return self::getBy('user_name', $name);
	}
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_UserType::make('user_type'),
			GDT_Username::make('user_name'),
			GDT_Level::make('user_level'),
			GDT_PasswordHash::make('user_password'),
		];
	}
	
}
