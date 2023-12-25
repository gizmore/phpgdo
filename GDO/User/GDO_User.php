<?php
declare(strict_types=1);
namespace GDO\User;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Index;
use GDO\Core\ModuleLoader;
use GDO\Date\Module_Date;
use GDO\Date\Time;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Card;

/**
 * The holy user class.
 * Most user related fields are in other module settings.
 *
 * @version 7.0.3
 * @since 1.0.0
 * @author gizmore
 *
 * @see GDO
 * @see GDT_User
 * @see Module_Date
 * @see Module_Language
 */
final class GDO_User extends GDO
{

	final public const GUEST_NAME_PREFIX = '~';

	#############
	### Cache ###
	#############
	# Instances
	private static ?self $SYSTEM = null;

	private static self $CURRENT;

    /**
     * @throws GDO_Exception
     */
    public static function system(): self
	{
		self::$SYSTEM ??= self::findById('1');
		return self::$SYSTEM;
	}

	###########
	### GDO ###
	###########

	/**
	 * @return GDO_User[]
	 */
	public static function admins(): array
	{
		static $admins;
		if ($admins === null)
		{
			$admins = self::withPermission(GDO_Permission::ADMIN);
		}
		return $admins;
	}

    /**
     * Get all users with a permisison.
     *
     * @return GDO_User[]
     * @throws GDO_DBException
     */
	public static function withPermission(string $permission): array
	{
		return self::withPermissionQuery($permission)->exec()->fetchAllObjects();
	}

	###############
	### Factory ###
	###############

	/**
	 * Get the query to get all users with a permisison.
	 */
	public static function withPermissionQuery(string $permission): Query
	{
		return GDO_UserPermission::table()->
		select('perm_user_id_t.*')->
		joinObject('perm_perm_id')->
		joinObject('perm_user_id')->
		fetchTable(self::table())->where('perm_name=' . quote($permission));
	}

    /**
     * @return GDO_User[]
     * @throws GDO_DBException
     */
	public static function staff(): array
	{
		static $staff;
		if ($staff === null)
		{
			$staff = self::withPermission(GDO_Permission::STAFF);
		}
		return $staff;
	}

	/**
	 * Get current user.
	 * Not neccisarilly via session!
	 */
	public static function current(): self
	{
		return self::$CURRENT;
	}

	/**
	 * Set the current user and their environment.
	 */
	public static function setCurrent(GDO_User $user): self
	{
		self::$CURRENT = $user;
		if (!$user->isGhost())
		{
			Time::setTimezone($user->getTimezone());
			Trans::setISO($user->getLangISO());
			Application::setUser($user);
		}
		return $user;
	}

	/**
	 * Get the appropiate timezone object for this user.
	 */
	public function getTimezone(): string
	{
		$tz = Module_Date::instance()->cfgUserTimezoneId($this);
		if ($tz > 1)
		{
			return $tz;
		}
		if (module_enabled('Session'))
		{
			return GDO_Session::get('timezone', Time::UTC);
		}
		return Time::UTC;
	}

	public function getLangISO(): ?string
	{
		return Module_Language::instance()->cfgUserLangID($this);
	}

    /**
     * Get a user by login, for auth mechanisms
     *
     * @TODO: getByLogin shall use a hook for mail module to login via email.
     * @throws GDO_DBException
     */
	public static function getByLogin(string $name): ?self
	{
		return self::getByName($name);
	}

    /**
     * @throws GDO_DBException
     */
    public static function getByName(string $name): ?self
	{
		return self::getBy('user_name', $name);
	}

    /**
     * @throws GDO_DBException
     */
    public static function getByGuestName(string $name): ?self
    {
        return self::getBy('user_guest_name', $name);
    }

	public static function getSingleWithSetting(string $moduleName, string $key, string $var, string $op = '='): ?self
	{
		$users = self::withSetting($moduleName, $key, $var, $op);
		return count($users) === 1 ? $users[0] : null;
	}

	/**
	 * Get all users with a specified setting.
	 * Optionally an SQL like match is performed.
	 *
	 * @return self[]
	 */
	public static function withSetting(string $moduleName, string $key, string $var, string $op='='): array
	{
		return self::withSettingResult($moduleName, $key, $var, $op)->fetchAllObjects();
	}

	public static function withSettingResult(string $moduleName, string $key, string $var, string $op = '='): Result
	{
		return GDO_UserSetting::usersWith($moduleName, $key, $var, $op);
	}

	##############
	### Getter ###
	##############

	/**
	 * @throws GDO_Exception
	 */
	public static function findSingleWithSetting(string $moduleName, string $key, string $var, string $op = '='): self
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
				throw new GDO_Exception('err_user_ambigious', [$c, $key, $op, $var]);
		}
	}

//	public function clearCache(): self
//	{
//		$currId = self::$CURRENT->getID();
//		self::$SYSTEM = null;
//		self::$CURRENT = self::ghost();
////		$this->tempReset();
//		parent::clearCache();
//		if ($currId)
//		{
//			self::$CURRENT = self::getById($currId);
//		}
////		else
////		{
////			self::$CURRENT = self::ghost();
////		}
//		return $this;
//	}

	public function getID(): ?string
	{
		return $this->gdoVar('user_id');
	}

	/**
	 * Get guest ghost user.
	 */
	public static function ghost(): self
	{
		return self::blank([
			'user_id' => '0',
			'user_type' => 'ghost',
		]);
	}

	public function isTestable(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('user_id'),
			GDT_UserType::make('user_type')->notNull(),
			GDT_Username::make('user_name')->notNull(false)->unique(),
			GDT_Username::make('user_guest_name')->unique()->notNull(false)->label('user_guest_name'),
			GDT_Level::make('user_level'),
			GDT_DeletedAt::make('user_deleted'),
			GDT_DeletedBy::make('user_deletor'),
			GDT_Index::make('usertype_index')->indexColumns('user_type'),
		];
	}

	#############
	### HREFs ###
	#############

	public function isSystem(): bool
	{
		return $this->isType(GDT_UserType::SYSTEM);
	}

	public function isType(string $type): bool { return $this->getType() === $type; }

	public function getType(): string { return $this->gdoVar('user_type'); }

	############
	### Type ###
	############

	public function href_edit(): string
	{
		return href('Admin', 'UserEdit', "&user={$this->getID()}");
	}

	public function href_perm_add(): string
	{
		return href('Admin', 'PermissionGrant', "&perm_user_id={$this->getID()}");
	}

	public function href_perm_revoke(): string
	{
		return href('Admin', 'PermissionRevoke', "&perm_user_id={$this->getID()}");
	}


	public function hrefProfile(): string
	{
		return href('User', 'Profile', "&for={$this->renderUserName()}");
	}

	public function renderUserName(): string
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

		$pp = $p . $p;
		if ($this->isGhost())
		{
			return $pp . t('ghost') . $pp;
		}

		return $pp . t('guest') . $pp;
	}

	public function getName(): ?string { return $this->gdoVar('user_name'); }

	public function getGuestName(): ?string { return $this->gdoVar('user_guest_name'); }

	public function isGhost(): bool { return $this->isType(GDT_UserType::GHOST); }

	public function renderName(): string
	{
		return $this->renderUserName();
	}

	################
	### Timezone ###
	################

	public function renderCard(): string
	{
		return $this->getCard()->render();
	}

	public function getCard(): GDT_Card
	{
		$card = GDT_Card::make('user-card-' . $this->getID());
		$card->titleRaw($this->renderProfileLink());
		return $card;
	}

	public function renderProfileLink(bool $nickname = true, ?int $avatar = 42, bool $level = true): string
	{
		return GDT_ProfileLink::make()->gdo($this)->avatarSize($avatar)->avatar(!!$avatar)->nickname($nickname)->user($this)->level($level)->render();
	}

	#############
	### Perms ###
	#############

	public function gdoAfterDelete(GDO $gdo): void
	{
		GDT_Hook::callWithIPC('UserDeleted', $gdo, $this->isPersisted());
	}

	public function isBot(): bool { return $this->isType(GDT_UserType::BOT); }

	public function isLink(): bool { return $this->isType(GDT_UserType::LINK); }

	public function isAnon(): bool { return $this->isGuest() && (!$this->getGuestName()); }

	public function isGuest(bool $andAuthenticated = true): bool
	{
		$a = $andAuthenticated;
		return $this->isType(GDT_UserType::GUEST) &&
			((!$a || !!$this->getGuestName())); # non guest
	}

	public function isMember(): bool
	{
		return $this->isType(GDT_UserType::MEMBER);
	}

	/**
	 * Check if it is a legit user.
	 * Either a guest with a name or a member.
	 */
	public function isUser(): bool
	{
		switch ($this->getType())
		{
			case GDT_UserType::GUEST:
				return !!$this->getGuestName();
			case GDT_UserType::MEMBER:
				return true;
			default:
				return false;
		}
	}

	public function hasTimezone(): bool
	{
		return $this->getTimezone() > 1;
	}

	public function getTimezoneObject(): \DateTimeZone
	{
		return Time::getTimezoneObject($this->getTimezone());
	}

	/**
	 * Not really checking auth status, but check for a user or guest name, then this user could be authed. if it's the current user you are surely authed. weird!
	 */
	public function isAuthenticated(): bool
	{
		return $this->getGuestName() || $this->getUserName();
	}

	public function getUserName(): ?string { return $this->gdoVar('user_name'); }

	/**
	 * @throws GDO_Exception
	 */
	public function hasPermissionID(string $permissionId = null): bool
	{
		if ($permissionId)
		{
			$permission = GDO_Permission::findById($permissionId);
			return $this->hasPermissionObject($permission);
		}
		return true;
	}

	##################
	### Persistent ###
	##################

	public function hasPermissionObject(GDO_Permission $permission): bool
	{
		return $this->hasPermission($permission->getName());
	}

	##############
	### Render ###
	##############

	/**
	 * Check if the user has at least one permisson.
	 */
	public function hasPermission(string $permissions): bool
	{
		$perms = $this->loadPermissions();
		foreach (explode(',', $permissions) as $permission)
		{
			if (in_array($permission, $perms, true))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Load user permissions from cache if possible.
	 */
	public function loadPermissions(): array
	{
		if ($this->isPersisted())
		{
			if (null === ($cache = $this->tempGet('gdo_permission')))
			{
				$cache = GDO_UserPermission::load($this);
				$this->tempSet('gdo_permission', $cache);
			}
			return $cache;
		}
		return GDT::EMPTY_ARRAY;
	}

	public function isAdmin(): bool
	{
		return $this->hasPermission(GDO_Permission::ADMIN);
	}

	public function isStaff(): bool
	{
		return $this->hasPermission(GDO_Permission::STAFF);
	}

	public function changedPermissions(): self
	{
		return $this->tempUnset('gdo_permission');
	}

	############
	### Card ###
	############

	public function getLevelAvailable(): int
	{
		return $this->getLevel() - (int)$this->getLevelSpent();
	}

	################
	### Settings ###
	################

	/**
	 * Get the effective userlevel for this user.
	 */
	public function getLevel(): int
	{
		return (int) $this->gdoVar('user_level');
	}

	public function getLevelSpent(): string
	{
		return $this->settingVar('User', 'level_spent');
	}

	public function settingVar(string $moduleName, string $key): ?string
	{
		return $this->setting($moduleName, $key)->getVar();
	}

	public function setting(string $moduleName, string $key): GDT
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		return $module->userSetting($this, $key);
	}

	/**
	 * Ensure user is persistent.
	 * This allows LoginAsGuest
	 */
	public function persistent(): self
	{
		if (!$this->isPersisted())
		{
			$this->setVar('user_type', GDT_UserType::GUEST);
			$this->insert();
			if (module_enabled('Session'))
			{
				if ($session = GDO_Session::instance())
				{
					$session->setVar('sess_user', $this->getID());
				}
			}
		}
		return $this;
	}

	public function getGender(): ?string
	{
		return $this->settingVar('User', 'gender');
	}

	public function hasSetting(string $moduleName, string $key): bool
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		return $module->hasSetting($key);
	}

	public function increaseSetting(string $moduleName, string $key, float $by = 1): self
	{
		$now = $this->settingVar($moduleName, $key);
		return $this->saveSettingVar($moduleName, $key, (string)($now + $by));
	}

	public function saveSettingVar(string $moduleName, string $key, ?string $var): self
	{
		if ($module = ModuleLoader::instance()->getModule($moduleName))
		{
			$module->saveUserSetting($this, $key, $var);
		}
		return $this;
	}

	/**
	 * Save all the ACL settings for a user's setting var.
	 */
	public function saveACLSettings(string $moduleName, string $key, string $relation, string $level = null, string $permission = null): self
	{
		$module = ModuleLoader::instance()->getModule($moduleName);
		$module->saveUserSettingACLRelation($this, $key, $relation);
		$module->saveUserSettingACLLevel($this, $key, $level);
		$module->saveUserSettingACLPermission($this, $key, $permission);
		$this->tempUnset('gdo_setting');
		return $this;
	}

	#############
	### Email ###
	#############

	public function hasMail(bool $confirmed = true): bool
	{
		return !!$this->getMail($confirmed);
	}

	/**
	 * Get the email address for a user.
	 * This requires the mail module.
	 *
	 * @param bool $confirmed if it shall only return confirmed addresses.
	 */
	public function getMail(bool $confirmed = true): ?string
	{
		$email = $this->settingVar('Mail', 'email');
		return $confirmed ?
			($this->settingVar('Mail', 'email_confirmed') ? $email : null)
			: $email;
	}

	public function getMailFormat(): string
	{
		return $this->settingVar('Mail', 'email_format');
	}

	#######################
	### Setting helpers ###
	#######################
	/**
	 * Get the credits for a user.
	 *
	 * @see Module_PaymentCredits.
	 */
	public function getCredits(): int
	{
		return $this->settingValue('PaymentCredits', 'credits');
	}

	public function settingValue(string $moduleName, string $key): float|object|int|bool|array|string|null
	{
		return $this->setting($moduleName, $key)->getValue();
	}

	#############
	### Hooks ###
	#############

	public function getCountryISO(): ?string
	{
		return $this->settingVar('Country', 'country_of_living');
	}

}

GDO_User::setCurrent(GDO_User::ghost());
