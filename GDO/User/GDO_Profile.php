<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;

/**
 * Profile generated from all module user settings.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class GDO_Profile extends GDO
{
	public function gdoColumns(): array
	{
		return [
			GDT_User::make('profile_user')->primary(),
			GDT_CreatedAt::make('profile_created'),
		];
	}

	public static function forUser(GDO_User $user) : self
	{
		$profile = self::blank([
			'profile_user' => $user->getID(),
			'profile_created' => $user->settingVar('Register', 'register_date'),
		]);
		return $profile;
	}

}
