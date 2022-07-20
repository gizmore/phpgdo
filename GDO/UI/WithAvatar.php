<?php
namespace GDO\UI;

use GDO\User\GDO_User;
use GDO\Avatar\GDT_Avatar;

/**
 * Add an avatar to a GDT.
 * 
 * @author gizmore
 */
trait WithAvatar
{
	public bool $avatar = false;
	public GDO_User $avatarUser;
	public function avatarUser(GDO_User $user) : self
	{
		$this->avatar = true;
		$this->avatarUser = $user;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderAvatar() : string
	{
		if (module_enabled('Avatar'))
		{
			if (isset($this->avatarUser))
			{
				return GDT_Avatar::make()->user($this->avatarUser)->render();
			}
		}
		return '';
	}
	
}
