<?php
namespace GDO\Core;

use GDO\Language\Trans;
use GDO\Util\Strings;
use GDO\Util\Regex;

/**
 * Very basic trait.
 *
 * Extract the short classname.
 *
 * Extract the gdo module name.
 * This is easy for any \GDO\ class.
 *
 * @TODO: If a class is not \GDO\ it is a 3rd party lib, and we could try to get the gdo module via the absolute realpath of the class definition via reflection.
 *
 * Provide human names for classes.
 *
 * Offers static and non static versions.
 *
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.0
 * @see WithName
 */
trait WithModule
{

	# #################
	# ## Short Name ###
	# #################
	public function gdoShortName(): string
	{
		return Strings::rsubstrFrom(get_class($this), '\\');
	}

	public static function gdoShortNameS(): string
	{
		return Strings::rsubstrFrom(static::class, '\\');
	}

	# #################
	# ## Class Name ###
	# #################
	public function gdoClassName(): string
	{
		return get_class($this);
	}

	public static function gdoClassNameS(): string
	{
		return static::class;
	}

	# #################
	# ## Human Name ###
	# #################
	/**
	 * Human readable classname.
	 */
	public function gdoHumanName(): string
	{
		return self::gdoHumanNameS();
	}

	public static function gdoHumanNameS(): string
	{
		$shortname = self::gdoShortNameS();
		$key = strtolower($shortname);
		if (Trans::hasKey($key))
		{
			return t($key);
		}
		return $shortname;
	}

	# #############
	# ## Module ###
	# #############
	public function getModule(): GDO_Module
	{
		$klass = get_class($this);
		return self::getModuleByKlass($klass);
	}

	public function getModuleName(): string
	{
		$klass = get_class($this);
		return self::getModuleNameByKlass($klass);
	}

	private static function getModuleByKlass(string $klass): GDO_Module
	{
		$moduleName = self::getModuleNameByKlass($klass);
		return ModuleLoader::instance()->getModule($moduleName, true, true);
	}

	private static function getModuleNameByKlass(string $klass): string
	{
		return Regex::firstMatch('#GDO\\\\([\\dA-Z_]+)#iD', $klass);
	}

}
