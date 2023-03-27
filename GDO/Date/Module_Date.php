<?php
namespace GDO\Date;

use GDO\Core\Application;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Date\Method\Timezone;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\User\GDT_ACLRelation;

/**
 * Date specific stuff.
 *
 * - timezone javascript detection. default: on
 * - sidebar timezone select in left panel. default: on
 * - Keeps timezone after user logout.
 * - Time utility helper
 *
 * @version 7.0.1
 * @since 6.10.1
 * @author gizmore
 * @see Time
 */
final class Module_Date extends GDO_Module
{

	public int $priority = 5;

	public function isCoreModule(): bool { return true; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/date'); }

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Timezone::make('tz_default')->initial('1')->notNull(),
			GDT_Checkbox::make('clock_sidebar')->initial('0')->notNull(),
			GDT_Checkbox::make('tz_probe_js')->initial('1')->notNull(),
			GDT_Checkbox::make('tz_sidebar_select')->initial('1')->notNull(),
		];
	}

	public function getACLDefaults(): array
	{
		return [
			'timezone' => [GDT_ACLRelation::FRIEND_FRIENDS, '0', null],
			'activity_accuracy' => [GDT_ACLRelation::FRIEND_FRIENDS, '0', null],
		];
	}

	public function getUserSettings(): array
	{
		return [
			GDT_Timezone::make('timezone')->initial('1')->notNull(),
			GDT_Duration::make('activity_accuracy')->initial('10m')->min(60)->max(Time::ONE_DAY * 2)->notNull()->label('activity_accuracy')->noacl(),
		];
	}

	public function getPrivacyRelatedFields(): array
	{
		return [
			GDT_Divider::make('privacy_info_date_module'),
			$this->setting('timezone'),
			$this->getSettingACL('timezone')->aclRelation,
			$this->setting('activity_accuracy'),
		];
	}

	public function onInstall(): void
	{
		Install::install($this);
	}

	################
	### Settings ###
	################

	public function onModuleInit(): void
	{
		if (
			(!Application::$INSTANCE->isInstall()) &&
			((!Application::$INSTANCE->isUnitTests()))
		)
		{
			$user = GDO_User::current();
			$timezone = $user->hasTimezone() ?
				$user->getTimezone() :
				$this->cfgTimezone()->getID();
			Time::setTimezone($timezone);
		}
	}

	public function cfgTimezone(): GDO_Timezone { return $this->getConfigValue('tz_default'); }

	public function onIncludeScripts(): void
	{
		if ($this->cfgProbeJS())
		{
			if (!GDO_User::current()->hasTimezone())
			{
				$this->addJS('js/gdo_timezone_probe.js');
			}
		}
	}

	public function cfgProbeJS(): string { return $this->getConfigVar('tz_probe_js'); }

	public function onInitSidebar(): void
	{
		if ($this->cfgSidebarSelect())
		{
			$user = GDO_User::current();
			if (!$user->hasTimezone())
			{
				if ($user->isPersisted() || module_enabled('Session'))
				{
					GDT_Page::instance()->leftBar()->addField(
						Timezone::make()->getForm()->slim());
				}
			}
		}
		if ($this->cfgClock())
		{
			$clock = GDT_DateDisplay::make('clock')->onlyDate()->initialNow();
			GDT_Page::instance()->leftBar()->addField($clock);
		}
	}

	############
	### Init ###
	############

	public function cfgSidebarSelect(): string { return $this->getConfigVar('tz_sidebar_select'); }

	public function cfgClock(): string { return $this->getConfigVar('clock_sidebar'); }

	public function cfgUserTimezoneId(GDO_User $user = null): string
	{
		$user = $user ?: GDO_User::current();
		return $this->userSettingVar($user, 'timezone');
	}

	public function cfgUserActivityAccuracy(GDO_User $user = null): int
	{
		$user = $user ? $user : GDO_User::current();
		return $this->userSettingValue($user, 'activity_accuracy');
	}

	#############
	### Hooks ###
	#############

	/**
	 * Save timezone on authenticated.
	 *
	 * @param GDO_User $user
	 */
	public function hookUserAuthenticated(GDO_User $user)
	{
		Module_Date::instance()->saveUserSetting($user, 'timezone', $user->getTimezone());
	}

//     public function hookUserLoggedOut(GDO_User $user)
//     {
//     	if ($tz = GDO_Timezone::getById($this->timezone))
//     	{
//     		Timezone::make()->setTimezone($tz, false);
//     	}
//     }

}
