<?php
namespace GDO\User;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Message;
use GDO\Net\GDT_Url;
use GDO\Date\GDT_Date;

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
	public int $priority = 4; # Start very early. Important for test chain.
	
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
	
	public function isCoreModule() : bool { return true; }
	public function onInstall() : void { OnInstall::onInstall(); }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/user'); }
// 	public function href_administrate_module() : ?string { return href('User', 'Admin'); }

	public function onInitSidebar() : void
	{
		if ($this->cfgSidebar())
		{
			$user = GDO_User::current();
			GDT_Page::instance()->rightBar()->addField(
				GDT_Link::make('link_your_profile')->href(
					$user->href_profile()));
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
		];
	}
	public function cfgSidebar() : bool { return $this->getConfigValue('hook_sidebar'); }
	public function cfgAboutMe() : bool { return $this->getConfigValue('about_me'); }
	
	################
	### Settings ###
	################
	/**
	 * profile views are default visible for all types, 0 score with any permission.
	 */
	public function getACLDefaults() : ?array
	{
		return [
			'gender' => [GDT_ACLRelation::FRIEND_FRIENDS, 0, null],
			'about_me' => [GDT_ACLRelation::MEMBERS, 0, null],
			'profile_views' => [GDT_ACLRelation::ALL, 0, null],
		];
	}
	
	public function getUserConfig() : array
	{
		return [
			GDT_Url::make('last_url')->noacl()->hidden(),
			GDT_Date::make('last_activity'),
			GDT_UInt::make('profile_views')->initial('0'),
		];
	}
		
	public function getUserSettings() : array
	{
		return [
			GDT_Gender::make('gender'),
		];
	}
	
	public function getUserSettingBlobs() : array
	{
		$settings = [];
		if ($this->cfgAboutMe())
		{
			$settings[] = GDT_Message::make('about_me')->label('about_me')->max(2048);
		}
		return $settings;
	}
	
}
