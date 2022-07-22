<?php
namespace GDO\User;

use GDO\Avatar\GDT_Avatar;

/**
 * Add an avatar to a GDT.
 * You need Module_Avatar to have anything rendered by this trait.
 * 
 * @author gizmore
 */
trait WithAvatar
{
	############
	### User ###
	############
	public GDO_User $avatarUser;
	public function avatarUser(GDO_User $user) : self
	{
		$this->avatarUser = $user;
		return $this;
	}
	
	public function hasAvatar() : bool
	{
		return isset($this->avatarUser);
	}
	
	##############
	### Render ###
	##############
	public function renderAvatar() : string
	{
		if (module_enabled('Avatar'))
		{
			if ($this->hasAvatar())
			{
				return $this->renderAvatarFor($this->avatarUser);
			}
		}
		return '';
	}
	
	private static GDT_Avatar $AVATAR_DUMMY;
	
	protected function renderAvatarFor(GDO_User $user) : string
	{
		if (!isset(self::$AVATAR_DUMMY))
		{
			self::$AVATAR_DUMMY = GDT_Avatar::make();
		}
		return self::$AVATAR_DUMMY->user($user)->render();
	}
	
}
