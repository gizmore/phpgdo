<?php
namespace GDO\User\Method;

use GDO\Core\GDO;
use GDO\UI\MethodCard;
use GDO\User\GDO_User;
use GDO\User\GDO_Profile;
use GDO\UI\GDT_Card;

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
	
	public function execute()
	{
		$user = $this->getObject();
		if (!$user)
		{
			return $this->error('err_no_data_yet');
		}
		$profile = GDO_Profile::forUser($user);
		return $this->executeFor($profile);
	}

	protected function createCard(GDT_Card $card) : void
	{
		$card->creatorHeader('profile_user', 'profile_created');
	}
	
}
