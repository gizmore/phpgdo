<?php
namespace GDO\User;

use GDO\Core\GDO;
use GDO\Core\GDO_Module;
use GDO\Session\GDO_Session;

/**
 * GDO_User related types and plugins.
 * 
 * @author gizmore
 * @version 7.0.0
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
			'Crypto', 'Date', 'Language', 'Net',
			'Session', 'Table', 'UI',
		];
	}
	
	public function getFriendencies() : array
	{
		return ['Cronjob'];
	}
	
	public function onInstall() : void { OnInstall::onInstall(); }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/user'); }
	public function href_administrate_module() { return href('User', 'Admin'); }

	public function getClasses() : array
	{
	    $classes = [
			GDO_UserSetting::class,
			GDO_UserSettingBlob::class,
	    ];
	    return $classes;
	}
	
}
