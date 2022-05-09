<?php
namespace GDO\Core;

use GDO\Util\Strings;
use GDO\Util\Regex;

/**
 * Extract the short classname.
 * 
 * Extract the module name.
 * This is easy for any \GDO\ class.
 * If a class is not \GDO\ it is a 3rd party lib, and we could try to get the gdo module via the absolute realpath of the class definition via reflection.
 * 
 * Offers static and non static versions.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithModule
{
	##################
	### Short Name ###
	##################
	public function gdoShortName() : string
	{
		$k = get_class($this);
		return Strings::rsubstrFrom($k, '\\', $k);
	}
	
	public static function gdoShortNameS() : string
	{
		$k = static::class;
		return Strings::rsubstrFrom($k, '\\', $k);
	}
	
	##############
	### Module ###
	##############
	public function getModuleName() : string
	{
		$klass = get_class($this);
		return self::getModuleNameByKlass($klass);
	}
	
	public function getModule() 
	{
		$klass = get_class($this);
		return self::getModuleByKlass($klass);
	}
	
// 	public static function getModuleS()
// 	{
// 		$klass = static::class;
// 		return self::getModuleByKlass($klass);
// 	}
	
	private static function getModuleByKlass(string $klass)
	{
		$moduleName = self::getModuleNameByKlass($klass);
		return ModuleLoader::instance()->getModule($moduleName, true, true);
	}
	
	private static function getModuleNameByKlass(string $klass)
	{
		return Regex::firstMatch('#GDO\\\\([\\dA-Z_]+)#iD', $klass);
	}
	
}
