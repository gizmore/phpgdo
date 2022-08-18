<?php
namespace GDO\User;

use GDO\Core\GDT_CreatedAt;
use GDO\Core\DTO;

/**
 * Profile generated from all module user settings.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class GDO_Profile extends DTO
{
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
