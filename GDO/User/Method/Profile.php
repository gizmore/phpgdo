<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\UI\MethodCard;
use GDO\User\GDO_User;

/**
 * Show a user's profile.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Profile extends MethodCard
{
	public function gdoTable(): GDO
	{
		return GDO_User::table();
	}
	
	public function getMethodTitle() : string
	{
		return t('mt_user_profile', [$this->getObject()->renderUserName()]);
	}
	
}
