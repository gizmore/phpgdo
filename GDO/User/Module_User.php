<?php
namespace GDO\User;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Message;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Color;
use GDO\Date\GDT_Timestamp;

/**
 * GDO_User related types and plugins.
 * 
 * Adds user config and settings: last url, gender, ...
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.4
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
	
	public function getDependencies() : array
	{
		return [
			'Core',
		];
	}
	
	public function getFriendencies() : array
	{
		return [
			'Avatar',
			'Cronjob',
			'Friends',
			'Session',
		];
	}
	
	public function getClasses() : array
	{
		$classes = [
			GDO_UserSetting::class,
			GDO_UserSettingBlob::class,
		];
		return $classes;
	}
	
	public function onInstall() : void { OnInstall::onInstall(); }
	public function isCoreModule() : bool { return true; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/user'); }

	public function onInitSidebar() : void
	{
		if ($this->cfgSidebar())
		{
			$user = GDO_User::current();
			if ($user->isUser()) {
				GDT_Page::instance()->rightBar()->addField(
					GDT_Link::make()->href($user->hrefProfile())->text('link_your_profile', [$user->renderUserName()]));
			}
		}
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Checkbox::make('hook_sidebar')->initial('1'),
			GDT_Checkbox::make('about_me')->initial('1'),
			GDT_Checkbox::make('fav_color')->initial('1'),
			GDT_Checkbox::make('acl_relations')->initial('1'),
			GDT_Checkbox::make('acl_levels')->initial('0'),
			GDT_Checkbox::make('acl_permissions')->initial('0'),
		];
	}
	public function cfgSidebar() : bool { return $this->getConfigValue('hook_sidebar'); }
	public function cfgAboutMe() : bool { return $this->getConfigValue('about_me'); }
	public function cfgFavColor() : bool { return $this->getConfigValue('fav_color'); }
	public function cfgACLRelations() : bool { return $this->getConfigValue('acl_relations'); }
	public function cfgACLLevels() : bool { return $this->getConfigValue('acl_levels'); }
	public function cfgACLPermissions() : bool { return $this->getConfigValue('acl_permissions'); }
	
	################
	### Settings ###
	################
	/**
	 * profile views are default visible for all types, 0 score with any permission.
	 */
	public function getACLDefaults() : ?array
	{
		return [
			'color' => [GDT_ACLRelation::GUESTS, 0, null],
			'gender' => [GDT_ACLRelation::FRIEND_FRIENDS, 0, null],
			'about_me' => [GDT_ACLRelation::MEMBERS, 0, null],
			'last_activity' => [GDT_ACLRelation::FRIEND_FRIENDS, 0, null],
			'profile_views' => [GDT_ACLRelation::ALL, 0, null],
			'probably_malicious' => [GDT_ACLRelation::HIDDEN, 0, null],
// 			'profile_visibility' => [GDT_ACLRelation::GUESTS, 0, null],
		];
	}
	
	public function getUserConfig() : array
	{
		return [
			GDT_Timestamp::make('last_activity'),
			GDT_Url::make('last_url')->noacl()->hidden()->allowInternal(),
			GDT_UInt::make('profile_views')->initial('0'),
			GDT_Checkbox::make('probably_malicious')->initial('0'),
		];
	}
		
	public function getUserSettings() : array
	{
		$settings = [
			GDT_Gender::make('gender'),
			GDT_ACLRelation::make('profile_visibility')->noacl(),
		];
		if ($this->cfgFavColor()) {
			$settings[] = GDT_Color::make('color');
		}
		return $settings;
	}
	
	public function getUserSettingBlobs() : array
	{
		$settings = [];
		if ($this->cfgAboutMe())
		{
			$settings[] = GDT_Message::make('about_me')->label('cfg_about_me')->max(2048);
		}
		return $settings;
	}
	
}
