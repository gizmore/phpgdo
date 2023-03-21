<?php
namespace GDO\User;

use GDO\Core\Module_Core;

/**
 * Install the default permissions and the system user.
 *
 * @version 7.0.2
 * @since 6.2.0
 * @author gizmore
 */
final class OnInstall
{

	public static function onInstall(): void
	{
		$permissions = [
			'cronjob' => 0,
			'staff' => 500,
			'admin' => 1000,
		];

		foreach ($permissions as $perm => $level)
		{
			GDO_Permission::create($perm, $level);
		}

		$user = GDO_User::blank([
			'user_id' => 1,
			'user_name' => 'system',
			'user_email' => GDO_BOT_EMAIL,
			'user_type' => 'system',
		])->softReplace();

		Module_Core::instance()->saveConfigVar('system_user', $user->getID());
	}

}
