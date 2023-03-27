<?php
namespace GDO\User;

use GDO\Avatar\GDT_Avatar;
use GDO\Core\GDT;

/**
 * Add an avatar to a GDT.
 * You need Module_Avatar to have anything rendered by this trait.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
trait WithAvatar
{

	private static GDT_Avatar $AVATAR_DUMMY;
	public GDO_User $avatarUser;

	############
	### User ###
	############
	public int $avatarSize = 32;

	public function avatarUser(GDO_User $user = null, int $size = 42): self
	{
		if ($user === null)
		{
			unset($this->avatarUser);
		}
		elseif (module_enabled('Avatar'))
		{
			$this->avatarUser = $user;
		}
		return $this->avatarSize($size);
	}

	############
	### Size ###
	############

	public function avatarSize(int $size): self
	{
		$this->avatarSize = $size;
		return $this;
	}

	public function renderAvatar(): ?string
	{
		if (module_enabled('Avatar'))
		{
			if ($this->hasAvatar())
			{
				return $this->renderAvatarFor($this->avatarUser);
			}
		}
		return GDT::EMPTY_STRING;
	}

	##############
	### Render ###
	##############

	public function hasAvatar(): bool
	{
		return isset($this->avatarUser);
	}

	protected function renderAvatarFor(GDO_User $user): ?string
	{
		if (!isset(self::$AVATAR_DUMMY))
		{
			self::$AVATAR_DUMMY = GDT_Avatar::make();
		}
		return self::$AVATAR_DUMMY->imageSize($this->avatarSize)->user($user)->render();
	}

}
