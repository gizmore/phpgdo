<?php
namespace GDO\UI;

use GDO\User\GDO_User;

trait WithAvatar
{
	public bool $avatar = false;
	public function avatar(bool $avatar)
	{
		$this->avatar = $avatar;
		return $this;
	}
	
	public GDO_User $avatarUser;
	public function avatarUser(GDO_User $user) : self
	{
		$this->avatarUser = $user;
		return $this->avatar(true);
	}
	
	public function renderAvatar()
	{
		if (module_enabled('Avatar'))
		{
			
		}
	}
	
}