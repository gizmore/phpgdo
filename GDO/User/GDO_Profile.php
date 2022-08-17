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
	public function gdoDTO() : bool
	{
		return true;
	}
	
	public function isTestable() : bool
	{
		return false;
	}
	
	public function gdoColumns(): array
	{
		return [
			GDT_User::make('profile_user')->primary(),
			GDT_CreatedAt::make('profile_activity'),
		];
	}
	
	public function getUser() : GDO_User { return $this->gdoValue('profile_user'); }

	public static function forUser(GDO_User $user) : self
	{
		$profile = self::blank([
			'profile_user' => $user->getID(),
			'profile_activity' => $user->gdoVar('user_last_activity'),
		]);
		return $profile;
	}

}
