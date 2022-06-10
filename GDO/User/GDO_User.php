<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Crypto\GDT_PasswordHash;
use GDO\Date\Time;
use GDO\Session\GDO_Session;
use GDO\Language\Trans;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_EditedAt;
use GDO\DB\Query;
use GDO\Date\Module_Date;
use GDO\Language\Module_Language;
use GDO\Mail\Module_Mail;

/**
 * The holy user class.
 * Most user related fields are in other module settings.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 1.0.0
 * @see GDO
 * @see Module_Date
 * @see Module_Language
 */
final class GDO_User extends GDO
{
	const GUEST_NAME_PREFIX = '~';
	const GHOST_NAME_PREFIX = '~~';
	
	#############
	### Cache ###
	#############
	# Instances
	private static ?self $SYSTEM = null;
	private static self $CURRENT;
	public function clearCache() : self
	{
		$currId = self::$CURRENT->getID();
		self::$SYSTEM = null;
		self::$CURRENT = self::ghost();
		$this->tempReset();
		parent::clearCache();
		if ($currId)
		{
			self::$CURRENT = self::getById($currId);
		}
		else
		{
			self::$CURRENT = self::ghost();
		}
		return $this;
	}
	
	###############
	### Factory ###
	###############
	public function isSystem() : bool
	{
		return $this->isType(GDT_UserType::SYSTEM);
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
	 * @return GDO_User[]
	 */
	public static function admins() : array
	{
		return self::withPermission('admin');
	}
	
	/**
	 * @return GDO_User[]
	 */
	public static function staff() : array
	{
		return self::withPermission('staff');
	}
	
	/**
	 * @return GDO_User[]
	 */
	public static function withPermission(string $permission) : array
	{
		return self::withPermissionQuery($permission)->exec()->fetchAllObjects();
	}
	
	/**
	 * Get all users with a permisison.
	 * @return self[]
	 */
	public static function withPermissionQuery(string $permission) : Query
	{
		return GDO_UserPermission::table()->
			select('perm_user_id_t.*')->
			joinObject('perm_perm_id')->
			joinObject('perm_user_id')->
			fetchTable(self::table())->where('perm_name=' . quote($permission));
	}
	
	/**
	 * Get current user.
	 * Not neccisarilly via session!
	 * @return self
	 */
	public static function current() : self
	{
		return self::$CURRENT;
	}
	
	public static function setCurrent(GDO_User $user, bool $switchLocale=false)
	{
		self::$CURRENT = $user;
		if ($switchLocale)
		{
			Time::setTimezone($user->getTimezone());
			Trans::setISO($user->getLangISO());
		}
		return $user;
	}
	
	/**
	 * Get guest ghost user.
	 */
	public static function ghost() : self
	{
		return self::blank(['user_id' => '0', 'user_type' => 'ghost']);
	}
	
	public static function getByName(string $name) : ?self
	{
		return self::getBy('user_name', $name);
	}
	
	/**
	 * Get a user by login, for auth mechanisms
	 * 
	 * @todo getByLogin shall use a hook for mail module to login via email.
	 * @return self
	 */
	public static function getByLogin(string $name) : ?self 
	{
		return self::getByName($name);
	}
	
	###########
	### GDO ###
	###########
	public function isTestable() : bool { return false; }
	
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_UserType::make('user_type'),
			GDT_Username::make('user_name')->unique(),
			GDT_Username::make('user_guest_name')->unique(),
			GDT_Level::make('user_level'),
			GDT_EditedAt::make('user_last_activity')->initial(Time::getDate()),
			GDT_DeletedAt::make('user_deleted'),
			GDT_DeletedBy::make('user_deletor'),
			GDT_PasswordHash::make('user_password'),
		];
	}
	
	##############
	### Getter ###
	##############
	public function hasMail() : bool { return !!Module_Mail::instance()->cfgUserEmailIsConfirmed($this); }

	public function getName() : ?string { return $this->gdoVar('user_name'); }
	public function getType() : string { return $this->gdoVar('user_type'); }
	public function getLangISO() : string { return Module_Language::instance()->cfgUserLangID(); }
	public function getUserName() : ?string { return $this->gdoVar('user_name'); }
	public function getGuestName() : ?string { return $this->gdoVar('user_guest_name'); }
	
	############
	### Type ###
	############
	public function isBot() : bool { return $this->isType(GDT_UserType::BOT); }
	public function isGhost() : bool { return $this->isType(GDT_UserType::GHOST); }
	public function isAnon() : bool { return $this->isGuest() && (!$this->getGuestName()); }
	public function isMember() : bool { return $this->isType(GDT_UserType::MEMBER); }
	public function isType($type) : bool { return $this->getType() === $type; }
	public function isGuest(bool $andAuthenticated=true) : bool
	{
		$a = $andAuthenticated;
		return $this->isType(GDT_UserType::GUEST) ?
			($a ? (!!$this->getGuestName()) : true) : # guest type checks for auth or true
			false; # non guest
	}
	
	/**
	 * Check if it is a legit user.
	 * Either a guest with a name or a member.
	 * 
	 * @return boolean
	 */
	public function isUser() : bool
	{
		switch ($this->getType())
		{
			case GDT_UserType::GUEST: return !!$this->getGuestName();
			case GDT_UserType::MEMBER: return true;
			default: return false;
		}
	}
	
	################
	### Timezone ###
	################
	/**
	 * Get the appropiate timezone object for this user.
	 */
	public function getTimezone() : string
	{
		$tz = Module_Date::instance()->cfgUserTimezoneId($this);
		if ($tz > 1)
		{
			return $tz;
		}
		if (class_exists('GDO\\Session\\GDO_Session', false))
		{
			return GDO_Session::get('timezone', '1');
		}
		return Time::UTC;
	}
	
	public function hasTimezone() : bool
	{
		return $this->getTimezone() > 1;
	}
	
	public function getTimezoneObject()
	{
		return Time::getTimezoneObject($this->getTimezone());
	}
	
	#############
	### Perms ###
	#############
	public function loadPermissions() : array
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
	
	public function hasPermissionID(string $permissionId=null) : bool
	{
		if ($permissionId)
		{
			$permission = GDO_Permission::findById($permissionId);
			return $this->hasPermissionObject($permission);
		}
		return true;
	}
	
	public function isAdmin() : bool { return $this->hasPermission('admin'); }
	public function isStaff() : bool { return $this->hasPermission('staff') || $this->hasPermission('admin'); }
	public function hasPermission(string $permission) : bool { return array_key_exists($permission, $this->loadPermissions()); }
	public function hasPermissionObject(GDO_Permission $permission) : bool { return $this->hasPermission($permission->getName()); }
	
	public function changedPermissions() : self
	{
		$this->tempUnset('gdo_permission');
		return $this->recache();
	}
	
	public function getLevel() : int
	{
		$level = $this->getVar('user_level');
		$permLevel = $this->getPermissionLevel();
		return (int)max([$level, $permLevel]);
	}
	
	public function getPermissionLevel() : int
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
	 * Ensure user is persistent.
	 * This allows LoginAsGuest 
	 */
	public function persistent() : self
	{
		if (!$this->isPersisted())
		{
			if (class_exists('GDO\\Session\\GDO_Session', false))
			{
				if ($session = GDO_Session::instance())
				{
					$this->setVar('user_type', self::GUEST);
					$this->insert();
					$session->setVar('sess_user', $this->getID());
				}
			}
		}
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderName() : string
	{
		return $this->renderUserName();
	}
	
	public function renderUserName() : string
	{
		if ($name = $this->getName())
		{
			return html($name);
		}
		if ($name = $this->getGuestName())
		{
			return '~' . html($name) . '~';
		}
		return t('guest');
	}

}

GDO_User::setCurrent(GDO_User::ghost());
