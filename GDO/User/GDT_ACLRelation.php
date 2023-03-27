<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\GDT_Enum;
use GDO\Friends\GDO_Friendship;

/**
 * An ACL field has default ACL options.
 * It checks access based on user relation and member status.
 * It helps to construct queries to reflect ACL permission.
 *
 * @version 7.0.3
 * @since 6.8.0
 * @author gizmore@wechall.net
 * @see GDT_ACL
 */
final class GDT_ACLRelation extends GDT_Enum
{

	final public const ALL = 'acl_all';
	final public const GUESTS = 'acl_guests';
	final public const MEMBERS = 'acl_members';
	final public const FRIENDS = 'acl_friends';
	final public const FRIEND_FRIENDS = 'acl_friend_friends';
	final public const NOONE = 'acl_noone';

	final public const HIDDEN = 'acl_hidden';

	protected function __construct()
	{
		parent::__construct();
		$this->enumValues(self::ALL, self::GUESTS, self::MEMBERS, self::FRIEND_FRIENDS, self::FRIENDS, self::NOONE, self::HIDDEN);
		$this->initial = self::NOONE;
		$this->notNull = true;
		$this->icon = 'eye';
	}

	/**
	 * Check if a userpair allows access for this setting.
	 */
	public function hasAccess(GDO_User $user, GDO_User $target, string &$reason): bool
	{
		switch ($this->var)
		{
			case self::ALL:
				return true;

			case self::GUESTS:
				if (!($result = $user->isUser()))
				{
					$reason = t('err_only_user_access');
				}
				return $result;

			case self::MEMBERS:
				if (!$result = $user->isMember())
				{
					$reason = t('err_only_member_access');
				}
				return $result;

			case self::FRIEND_FRIENDS:
				$result = module_enabled('Friends') && GDO_Friendship::isFriendFriend($user, $target);
				if (!$result)
				{
					$reason = t('err_only_friend_friend_access');
				}
				return $result;

			case self::FRIENDS:
				$result = module_enabled('Friends') && GDO_Friendship::areRelated($user, $target);
				if (!$result)
				{
					$reason = t('err_only_friend_access');
				}
				return $result;

			case self::NOONE:
				$reason = t('err_only_private_access');
				return false;

			case self::HIDDEN:
				# Show nothing
				return false;

			default: # Should never happen.
				$reason = t('err_unknown_acl_setting', [$this->var]);
				return false;
		}
	}

}
