<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Hook;
use GDO\Date\Time;
use GDO\Session\GDO_Session;
use GDO\Core\GDT_DeletedAt;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Date\Module_Date;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\Core\GDO_Error;
use GDO\UI\GDT_Card;

/**
 * The holy user class.
 * Most user related fields are in other module settings.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 1.0.0
 * @see GDO
 * @see GDT_User
 * @see Module_Date
 * @see Module_Language
 */
final class GDO_User extends GDO
{
	const GUEST_NAME_PREFIX = '~';
	
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
			GDT_Username::make('user_guest_name')->unique()->notNull(false)->label('user_guest_name'),
			GDT_Level::make('user_level'),
			GDT_DeletedAt::make('user_deleted'),
			GDT_DeletedBy::make('user_deletor'),
		];
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
	 * Get all users with a permisison.
	 * @return GDO_User[]
	 */
	public static function withPermission(string $permission) : array
	{
		return self::withPermissionQuery($permission)->exec()->fetchAllObjects();
	}
	
	/**
	 * Get the query to get all users with a permisison.
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
	 */
	public static function current() : self
	{
		return self::$CURRENT;
	}
	
	/**
	 * Set the current user and their environment.
	 */
	public static function setCurrent(GDO_User $user) : self
	{
		self::$CURRENT = $user;
		Time::setTimezone($user->getTimezone());
		Trans::setISO($user->getLangISO());
		return $user;
	}
	
	/**
	 * Get guest ghost user.
	 */
	public static function ghost() : self
	{
		return self::blank([
			'user_id' => '0',
			'user_type' => 'ghost'
		]);
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
	
	##############
	### Getter ###
	##############
	public function getID() : ?string { return $this->gdoVar('user_id'); }
	public function getName() : ?string { return $this->gdoVar('user_name'); }
	public function getType() : string { return $this->gdoVar('user_type'); }
	public function getLangISO() : string { return Module_Language::instance()->cfgUserLangID($this); }
	public function getUserName() : ?string { return $this->gdoVar('user_name'); }
	public function getGuestName() : ?string { return $this->gdoVar('user_guest_name'); }
	
	#############
	### HREFs ###
	#############
	public function href_edit() : string
	{
		return href('Admin', 'UserEdit', "&user={$this->getID()}");
	}
	
	public function href_perm_add() : string
	{
		return href('Admin', 'PermissionGrant', "&perm_user_id={$this->getID()}");
	}
	
	public function hrefProfile() : string
	{
		return href('User', 'Profile', "&for={$this->renderUserName()}");
	}
	
	############
	### Type ###
	############
	public function isBot() : bool { return $this->isType(GDT_UserType::BOT); }
	public function isLink() : bool { return $this->isType(GDT_UserType::LINK); }
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
		if (module_enabled('Session'))
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
	/**
	 * Not really checking auth status, but check for a user or guest name, then this user could be authed. if it's the current user you are surely authed. weird!
	 */
	public function isAuthenticated() : bool
	{
		return $this->getGuestName() || $this->getUserName();
	}
	
	/**
	 * Load user permissions from cache if possible.
	 */
	public function loadPermissions() : array
	{
		if ($this->isPersisted())
		{
			if (null === ($cache = $this->tempGet('gdo_permission')))
			{
				$cache = GDO_UserPermission::load($this);
				$this->tempSet('gdo_permission', $cache);
				$this->recache();
			}
			return $cache;
		}
		return GDT::EMPTY_ARRAY;
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
	
	/**
	 * Check if the user has at least one permisson.
	 * @param string $permission CSV permissions
	 */
	public function hasPermission(string $permissions) : bool
	{
		$perms = $this->loadPermissions();
		foreach (explode(',', $permissions) as $permission)
		{
			if (array_key_exists($permission, $perms))
			{
				return true;
			}
		}
		return false;
	}

	public function hasPermissionObject(GDO_Permission $permission) : bool
	{
		return $this->hasPermission($permission->getName());
	}
	
	public function changedPermissions() : self
	{
		$this->tempUnset('gdo_permission');
		return $this->recache();
	}
	
	/**
	 * Get the effective userlevel for this user.
	 */
	public function getLevel() : int
	{
		$level = $this->gdoVar('user_level');
		$permLevel = $this->getPermissionLevel();
		return $level + $permLevel;
	}
	
	public function getLevelSpent(): int
	{
		return $this->settingVar('User', 'level_spent');
	}
	
	public function getLevelAvailable(): int
	{
		return $this->getLevel() - $this->getLevelSpent();
	}
	
	/**
	 * Get the highest level for all permissions.
	 */
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
			if (module_enabled('Session'))
			{
				if ($session = GDO_Session::instance())
				{
					$this->setVar('user_type', GDT_UserType::GUEST);
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
		
		$p = self::GUEST_NAME_PREFIX;
		if ($name = $this->getGuestName())
		{
			return $p . html($name) . $p;
		}
		
		$pp = $p.$p;
		if ($this->isGhost())
		{
			return $pp . t('ghost') . $pp;
		}
		return $pp . t('guest') . $pp;
	}
	
// 	public function getProfileLink(bool $nickname=true, ?int $avatar=42, bool $level=true) : GDT_ProfileLink
// 	{
// 		$link = GDT_ProfileLink::make()->gdo($this);
// 		$link->nickname($nickname);
// 		if ($avatar > 0)
// 		{
// 			$link->avatarUser($this, $avatar);
// 		}
// 		if ($level)
// 		{
// 			$link->tooltip('tt_user_profile_link', [$this->renderUserName(), $this->getLevel()]);
// 		}
// 		return $link;
// 	}

	public function renderProfileLink(bool $nickname=true, ?int $avatar=42, bool $level=true) : string
	{
		return GDT_ProfileLink::make()->gdo($this)->avatarSize($avatar)->avatar(!!$avatar)->nickname($nickname)->user($this)->level($level)->render();
	}
	
	public function getGender() : ?string
	{
		return $this->settingVar('User', 'gender');
	}
	
	public function renderCard(): string
	{
		return $this->getCard()->render();
	}
	
	############
	### Card ###
	############
	public function getCard(): GDT_Card
	{
		$card = GDT_Card::make('user-card-'.$this->getID());
		$card->titleRaw($this->renderProfileLink());
		return $card;
	}
	
	################
	### Settings ###
	################
	public function setting(string $moduleName, string $key) : GDT
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		return $module->userSetting($this, $key);
	}
	
	public function hasSetting(string $moduleName, string $key): bool
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		return $module->hasSetting($key);
	}
	
	public function settingVar(string $moduleName, string $key) : ?string
	{
		return $this->setting($moduleName, $key)->getVar();
	}
	
	public function settingValue(string $moduleName, string $key)
	{
		return $this->setting($moduleName, $key)->getValue();
	}
	
	public function saveSettingVar(string $moduleName, string $key, ?string $var) : self
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		$module->saveUserSetting($this, $key, $var);
		return $this;
	}
	
	public function increaseSetting(string $moduleName, string $key, float $by=1) : self
	{
		$now = $this->settingVar($moduleName, $key);
		return $this->saveSettingVar($moduleName, $key, $now + $by);
	}
	
	/**
	 * Save all the ACL settings for a user's setting var.
	 */
	public function saveACLSettings(string $moduleName, string $key, string $relation, int $level=0, string $permission=null): self
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		$module->saveUserSettingACLRelation($this, $key, $relation);
		$module->saveUserSettingACLLevel($this, $key, $level);
		$module->saveUserSettingACLPermission($this, $key, $permission);
		$this->tempUnset('gdo_setting');
		$this->recache();
		return $this;
	}
	
	/**
	 * Get all users with a specified setting.
	 * Optionally an SQL like match is performed.
	 * @return self[]
	 */
	public static function withSetting(string $moduleName, string $key, string $var, string $op='=') : array
	{
		return self::withSettingResult($moduleName, $key, $var, $op)->fetchAllObjects();
	}
	
	public static function withSettingResult(string $moduleName, string $key, string $var, string $op='=') : Result
	{
		return GDO_UserSetting::usersWith($moduleName, $key, $var, $op);
	}
	
	public static function getSingleWithSetting(string $moduleName, string $key, string $var, string $op='=') : ?self
	{
		$users = self::withSetting($moduleName, $key, $var, $op);
		return count($users) === 1 ? $users[0] : null;
	}

	public static function findSingleWithSetting(string $moduleName, string $key, string $var, string $op='=') : self
	{
		$users = self::withSetting($moduleName, $key, $var, $op);
		$c = count($users);
		switch ($c)
		{
			case 0:
				self::notFoundException("$key = $var");
			case 1:
				return $users[0];
			default:
				throw new GDO_Error('err_user_ambigious', [$c, $key, $op, $var]);
		}
	}
	
	#############
	### Email ###
	#############
	public function hasMail(bool $confirmed=true)
	{
		return !!$this->getMail($confirmed);
	}
	
	/**
	 * Get the email address for a user.
	 * This requires the mail module.
	 * 
	 * @param bool $confirmed if it shall only return confirmed addresses.
	 */
	public function getMail(bool $confirmed=true) : ?string
	{
		$email = $this->settingVar('Mail', 'email');
		return $confirmed ? ($this->settingVar('Mail', 'email_confirmed') ? $email : null) : $email;
	}

	public function getMailFormat() : string
	{
		return $this->settingVar('Mail', 'email_format');
	}
	
	#######################
	### Setting helpers ###
	#######################
	/**
	 * Get the credits for a user.
	 * @see Module_PaymentCredits.
	 */
	public function getCredits() : int
	{
		return $this->settingValue('PaymentCredits', 'credits');
	}
	
	public function getCountryISO() : ?string
	{
		return $this->settingVar('Country', 'country_of_living');
	}
	
	#############
	### Hooks ###
	#############
	public function gdoAfterDelete(GDO $gdo): void
	{
		GDT_Hook::callWithIPC('UserDeleted', $gdo, $this->isPersisted());
	}
	
}

GDO_User::setCurrent(GDO_User::ghost());
