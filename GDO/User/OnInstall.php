<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\Module_Core;

/**
 * Install the default permissions and the system user.
 *
 * @version 7.0.3
 * @since 6.2.0
 * @author gizmore
 */
final class OnInstall
{

	public static function onInstall(): void
	{
		static $permissions = [
			GDO_Permission::ADMIN,
			GDO_Permission::STAFF,
			GDO_Permission::CRONJOB,
		];

		foreach ($permissions as $perm)
		{
			GDO_Permission::create($perm);
		}

		$user = GDO_User::blank([
			'user_id' => '1',
			'user_name' => 'system',
			'user_email' => GDO_BOT_EMAIL,
			'user_type' => 'system',
		])->softReplace();

		Module_Core::instance()->saveConfigVar('system_user', $user->getID());
	}

}
