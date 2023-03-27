<?php
namespace GDO\User;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_UInt;
use GDO\Date\GDT_Timestamp;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Color;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Menu;
use GDO\UI\GDT_Page;

/**
 * GDO_User related types and plugins.
 *
 * Adds user config and settings: last url, gender, ...
 *
 * @version 7.0.1
 * @since 3.0.4
 * @author gizmore
 * @see GDO
 * @see GDO_User
 */
final class Module_User extends GDO_Module
{

	##############
	### Module ###
	##############
	# Start very early. Important for test chain.
	public int $priority = 4;

	public function getDependencies(): array
	{
		return [
			'Core',
		];
	}

	public function getFriendencies(): array
	{
		return [
			'Account', 'Avatar', 'Cronjob',
			'Friends', 'Session',
		];
	}

	public function getClasses(): array
	{
		$classes = [
			GDO_UserSetting::class,
			GDO_UserSettingBlob::class,
		];
		return $classes;
	}

	public function onInstall(): void { OnInstall::onInstall(); }

	public function isCoreModule(): bool { return true; }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/user'); }

	public function onInitSidebar(): void
	{
		if ($this->cfgSidebar())
		{
			$user = GDO_User::current();
			if ($user->isUser())
			{
				$menu = GDT_Menu::make('menu_profile')->vertical();
				GDT_Page::instance()->rightBar()->addField($menu);
				$menu->addField(GDT_Link::make()->href($user->hrefProfile())->text('link_your_profile', [
					$user->renderUserName()]));
			}
		}
	}

	##############
	### Config ###
	##############

	public function cfgSidebar(): bool { return $this->getConfigValue('hook_sidebar'); }

	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('hook_sidebar')->initial('1'),
			GDT_Checkbox::make('fav_color')->initial('1'),
			GDT_Checkbox::make('acl_relations')->initial('1'),
			GDT_Checkbox::make('acl_levels')->initial('0'),
			GDT_Checkbox::make('acl_permissions')->initial('0'),
		];
	}

	/**
	 * profile views are default visible for all types, 0 score with any permission.
	 */
	public function getACLDefaults(): array
	{
		return [
			'color' => [GDT_ACLRelation::GUESTS, '0', null],
			'gender' => [GDT_ACLRelation::FRIEND_FRIENDS, '0', null],
			'last_activity' => [GDT_ACLRelation::FRIEND_FRIENDS, '0', null],
			'profile_views' => [GDT_ACLRelation::ALL, '0', null],
			'level_spent' => [GDT_ACLRelation::HIDDEN, '0', null],
			'probably_malicious' => [GDT_ACLRelation::HIDDEN, '0', null],
		];
	}

	public function getUserConfig(): array
	{
		return [
			GDT_Timestamp::make('last_activity'),
			GDT_Url::make('last_url')->noacl()->hidden()->allowInternal(),
			GDT_UInt::make('profile_views')->initial('0'),
			GDT_Level::make('level_spent')->initial('0')->label('level_spent')->tooltip('tt_level_spent')->noacl(),
			GDT_Checkbox::make('probably_malicious')->initial('0')->noacl()->hidden(),
		];
	}

	public function getUserSettings(): array
	{
		$settings = [
			GDT_Gender::make('gender'),
			GDT_ACLRelation::make('profile_visibility')->initial(GDT_ACLRelation::ALL)->noacl(),
		];
		if ($this->cfgFavColor())
		{
			$settings[] = GDT_Color::make('color')->label('favorite_color');
		}
		return $settings;
	}

	public function cfgFavColor(): bool { return $this->getConfigValue('fav_color'); }

	################
	### Settings ###
	################

	public function getPrivacyRelatedFields(): array
	{
		return [
			$this->setting('last_activity'),
			$this->getSettingACL('last_activity')->aclRelation,
			$this->setting('last_url'),
		];
	}

	public function cfgACLRelations(): bool { return $this->getConfigValue('acl_relations'); }

	public function cfgACLLevels(): bool { return $this->getConfigValue('acl_levels'); }

	public function cfgACLPermissions(): bool { return $this->getConfigValue('acl_permissions'); }

}
