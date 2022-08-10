<?php
namespace GDO\User;

use GDO\Avatar\GDT_Avatar;

/**
 * Add an avatar to a GDT.
 * You need Module_Avatar to have anything rendered by this trait.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
trait WithAvatar
{
	private static GDT_Avatar $AVATAR_DUMMY;
	
	public function hasAvatar() : bool
	{
		return isset($this->avatarUser);
	}
	
	############
	### User ###
	############
	public GDO_User $avatarUser;
	public function avatarUser(GDO_User $user=null, int $size=32) : self
	{
		if ($user === null)
		{
			unset($this->avatarUser);
		}
		else
		{
			$this->avatarUser = $user;
		}
		return $this->avatarSize($size);
	}
	
	############
	### Size ###
	############
	public int $avatarSize = 32;
	public function avatarSize(int $size) : self
	{
		$this->avatarSize = $size;
		return $this;
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
	
	protected function renderAvatarFor(GDO_User $user) : string
	{
		if (!isset(self::$AVATAR_DUMMY))
		{
			self::$AVATAR_DUMMY = GDT_Avatar::make();
		}
		return self::$AVATAR_DUMMY->imageSize($this->avatarSize)->user($user)->renderHTML();
	}
	
}
