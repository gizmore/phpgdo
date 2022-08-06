<?php
namespace GDO\User;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;

/**
 * GDO_User related types and plugins.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 */
final class Module_User extends GDO_Module
{
	##############
	### Module ###
	##############
	public int $priority = 4; # start very early
	
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
	public function href_administrate_module() : ?string { return href('User', 'Admin'); }

	public function onInitSidebar() : void
	{
		if ($this->cfgSidebar())
		{
			$uid = GDO_User::current()->getID();
			GDT_Page::instance()->rightBar()->addField(
				GDT_Link::make('link_your_profile')->href(
					href('User', 'Profile', "&id=$uid")));
		}
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Checkbox::make('hook_sidebar')->initial('1'),
		];
	}
	public function cfgSidebar() : bool { return $this->getConfigValue('hook_sidebar'); }
	
	################
	### Settings ###
	################
	/**
	 * profile views are default visible for all types, 0 score with any permission.
	 */
	public function getACLDefaults() : ?array
	{
		return [
			'profile_views' => ['acl_all', 0, null],
		];
	}
	
	public function getUserConfig() : array
	{
		return [
			GDT_UInt::make('profile_views')->initial('0'),
		];
	}
	
	public function getUserSettings() : array
	{
		return [
			GDT_Gender::make('gender'),
		];
	}
	
}
