<?php
namespace GDO\User;

use GDO\Crypto\BCrypt;
use GDO\Core\Module_Core;

/**
 * Install the default permissions.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
final class OnInstall
{
	public static function onInstall() : void
	{
	    $permissions = array(
	        'cronjob' => 0,
	        'staff' => 500,
	        'admin' => 1000,
	    );
	    
	    foreach ($permissions as $perm => $level)
	    {
	        GDO_Permission::create($perm, $level);
	    }
	    
// 		if (!($user = GDO_User::getByName('system')))
// 		{
			$user = GDO_User::blank([
				'user_id' => 1,
				'user_name' => 'system',
				'user_email' => GDO_BOT_EMAIL,
				'user_type' => 'system',
			])->softReplace();
// 		}

		Module_Core::instance()->saveConfigVar('system_user', $user->getID());
	}

}
