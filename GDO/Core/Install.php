<?php
namespace GDO\Core;

use GDO\Util\FileUtil;

/**
 * Install the core module.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Install 
{
	
	public static function onInstall(Module_Core $module) : void
	{
		self::setupFileSystem();
	}
	
	private static function setupFileSystem() : void
	{
		FileUtil::createDir(GDO_PATH . 'assets');
		FileUtil::createDir(GDO_TEMP_PATH);
		FileUtil::createDir(GDO_TEMP_PATH . 'cache');
		FileUtil::createFile(GDO_TEMP_PATH . 'ipc.socket');
	}
	
}
