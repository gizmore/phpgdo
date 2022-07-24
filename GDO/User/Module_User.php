<?php
namespace GDO\User;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;

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
		];
	}
	
	public function isCoreModule() : bool { return true; }
	public function onInstall() : void { OnInstall::onInstall(); }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/user'); }
	public function href_administrate_module() : ?string { return href('User', 'Admin'); }

	public function getClasses() : array
	{
	    $classes = [
			GDO_UserSetting::class,
			GDO_UserSettingBlob::class,
	    ];
	    return $classes;
	}
	
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
