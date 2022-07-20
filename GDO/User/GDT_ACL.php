<?php
namespace GDO\User;

use GDO\Core\GDT_Enum;
use GDO\Core\GDO_Exception;
use GDO\DB\Query;
use GDO\Friends\GDO_Friendship;

/**
 * An ACL field has default ACL options.
 * It checks access based on user relation and member status.
 * It helps to construct queries to reflect ACL permission.
 * 
 * @author gizmore@wechall.net
 * @version 7.0.1
 * @since 6.8.0
 */
final class GDT_ACL extends GDT_Enum
{
	const ALL = 'acl_all';
	const GUESTS = 'acl_guests';
	const MEMBERS = 'acl_members';
	const FRIENDS = 'acl_friends';
	const FRIEND_FRIENDS = 'acl_friend_friends';
	const NOONE = 'acl_noone';
	
	protected function __construct()
	{
	    parent::__construct();
	    $this->enumValues(self::ALL, self::GUESTS, self::MEMBERS, self::FRIEND_FRIENDS, self::FRIENDS, self::NOONE);
		$this->initial = self::NOONE;
		$this->notNull = true;
		$this->icon = 'eye';
	}
	
	/**
	 * Check if a userpair allows access for this setting.
	 * @param GDO_User $user
	 * @param GDO_User $target
	 * @param string $reason
	 * @throws GDO_Exception
	 * @return boolean
	 */
	public function hasAccess(GDO_User $user, GDO_User $target, &$reason, $throwException=true)
	{
		# Self is fine
		if ($user === $target) { return true; }
		
		# Other cases
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
				$result = module_enabled('Friends') ? GDO_Friendship::isFriendFriend($user, $target) : false;
				if (!$result)
				{
					$reason = t('err_only_friend_friend_access');
				}
				return $result;
				
			case self::FRIENDS:
				$result = module_enabled('Friends') ? GDO_Friendship::areRelated($user, $target) : false;
				if (!$result)
				{
					$reason = t('err_only_friend_access');
				}
				return $result;
				
			case self::NOONE:
				$reason = t('err_only_private_access');
				return false;
			
			default: # Should never happen.
				$reason = t('err_unknown_acl_setting', [$this->var]);
				if ($throwException)
				{
					throw new GDO_Exception($reason);
				}
				return false;
		}
	}
	
	/**
	 * Add where conditions to a query that reflect acl settings.
	 * @param Query $query
	 * @param GDO_User $user
	 * @param string $creatorColumn
	 * @return self
	 */
	public function aclQuery(Query $query, GDO_User $user, $creatorColumn)
	{
		# All
		$idf = $this->identifier();
		$condition = "$idf = 'acl_all'";

		# Members
		if ($user->isMember())
		{
			$condition .= " OR $idf = 'acl_members'";
		}
		
		# Friends and own require a owner column
		if ($creatorColumn)
		{
			# Own
			$uid = $user->getID();
			$condition .= " OR $creatorColumn = {$uid}";

			# Friends
			if (module_enabled('Friends'))
			{
				$subquery = "SELECT 1 FROM gdo_friendship WHERE friend_user=$uid AND friend_friend=$creatorColumn";
				$condition .= " OR ( $idf = 'acl_friends' AND ( $subquery ) )";
			}
		}
		
		# Apply condition
		$query->where($condition);
		return $this;
	}
	
}
