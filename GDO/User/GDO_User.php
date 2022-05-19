<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Crypto\GDT_PasswordHash;
use GDO\Date\GDT_Timezone;
use GDO\Date\Time;
use GDO\Session\GDO_Session;
use GDO\Language\GDT_Language;

/**
 * The holy user class.
 * Most user related fields are in other module settings.
 * 
 * @see GDO
 * @see GDT
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 1.0.0
 */
final class GDO_User extends GDO
{
	const GUEST_NAME_PREFIX = '~';
	const GHOST_NAME_PREFIX = '~~';
	
	# Instances
	private static self $SYSTEM;
	private static self $CURRENT;
	
	###############
	### Factory ###
	###############
	public function isSystem() : bool
	{
		return $this->getID() === '1';
	}
	
	public static function system() : self
	{
		if (!isset(self::$SYSTEM))
		{
			self::$SYSTEM = self::findById('1');
		}
		return self::$SYSTEM;
	}
	
	/**
	 * Get current user.
	 * Not neccisarilly via session!
	 * @return self
	 */
	public static function current() : self
	{
		if (!isset(self::$CURRENT))
		{
			self::$CURRENT = self::ghost();
		}
		return self::$CURRENT;
	}
	
	public static function setCurrent(GDO_User $user)
	{
		self::$CURRENT = $user;
		return $user;
	}
	
	/**
	 * Get guest ghost user.
	 * @return self
	 */
	public static function ghost() : self
	{
		return self::blank(['user_id' => '0', 'user_type' => 'ghost']);
	}
	
	public static function getByName(string $name) : ?self
	{
		return self::getBy('user_name', $name);
	}
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_UserType::make('user_type'),
			GDT_Username::make('user_name'),
			GDT_Username::make('user_guest_name')->unique(),
			GDT_Language::make('user_language')->notNull()->initial(GDO_LANGUAGE),
			GDT_Timezone::make('user_timezone')->notNull()->initial('1')->cascadeRestrict(),
			GDT_Level::make('user_level'),
			GDT_PasswordHash::make('user_password'),
		];
	}
	
	##############
	### Getter ###
	##############
	public function getType() : string { return $this->gdoVar('user_type'); }
	public function getLangISO() : string { return $this->gdoVar('user_language'); }
	public function getGuestName() : ?string { return $this->gdoVar('user_guest_name'); }
	
	### Type ###
	############
	
	public function isBot() { return $this->isType(GDT_UserType::BOT); }
	public function isGhost() { return $this->isType(GDT_UserType::GHOST); }
	public function isAnon() { return $this->isGuest() && (!$this->getGuestName()); }
	public function isMember() { return $this->isType(GDT_UserType::MEMBER); }
	public function isType($type) { return $this->getType() === $type; }
	public function isGuest(bool $andAuthenticated=true) : bool
	{
		$a = $andAuthenticated;
		return $this->isType(GDT_UserType::GUEST) ?
			($a ? (!!$this->getGuestName()) : true) : # guest type checks for auth or true
			false; # non guest
	}
	
	
	
	################
	### Timezone ###
	################
	/**
	 * Get the appropiate timezone object for this user.
	 * @return \DateTimeZone
	 */
	public function getTimezone() : string { return $this->getVar('user_timezone') > 1 ? $this->getVar('user_timezone') : GDO_Session::get('timezone', '1'); }
	
	public function getTimezoneObject()
	{
		return Time::getTimezoneObject($this->getTimezone());
	}
	
	#############
	### Perms ###
	#############
	public function loadPermissions()
	{
		if ($this->isPersisted())
		{
			if (null === ($cache = $this->tempGet('gdo_permission')))
			{
				if ($cache = GDO_UserPermission::load($this))
				{
					$this->tempSet('gdo_permission', $cache);
					$this->recache();
				}
			}
			return $cache;
		}
		return [];
	}
	public function hasPermissionID($permissionId)
	{
		if ($permissionId)
		{
			$permission = GDO_Permission::findById($permissionId);
			return $this->hasPermissionObject($permission);
		}
		return true;
	}
	public function hasPermissionObject(GDO_Permission $permission) { return $this->hasPermission($permission->getName()); }
	public function hasPermission($permission) { return array_key_exists($permission, $this->loadPermissions()); }
	public function isAdmin() { return $this->hasPermission('admin'); }
	public function isStaff() { return $this->hasPermission('staff') || $this->hasPermission('admin'); }
	public function changedPermissions()
	{
		$this->tempUnset('gdo_permission');
		return $this->recache();
	}
	
	public function getLevel()
	{
		$level = $this->getVar('user_level');
		$permLevel = $this->getPermissionLevel();
		return (int)max([$level, $permLevel]);
	}
	
	public function getPermissionLevel()
	{
		$max = 0;
		if ($perms = $this->loadPermissions())
		{
			foreach ($perms as $level)
			{
				if ($level > $max)
				{
					$max = $level;
				}
			}
		}
		return $max;
	}
	
	##################
	### Persistent ###
	##################
	/**
	 * @return GDO_User
	 */
	public function persistent()
	{
		if ($session = GDO_Session::instance())
		{
			if ($this->isGhost())
			{
				$this->setVar('user_type', self::GUEST);
				$this->insert();
				$session->setVar('sess_user', $this->getID());
			}
		}
		return $this;
	}
	
	
}
