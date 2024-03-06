<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Official registered gdo6 module mapping.
 * Installer can spit out repo urls for modules.
 * Some modules have multiple providers, like gdo6-session-db and gdo6-session-cookie.
 * Both provide Module_Session.
 *
 * You can generate providers and dependenices with providers.php and provider_dependenciews.php
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class ModuleProviders
{

	final public const GIT_PROVIDER = 'https://github.com/gizmore/';
	/**
	 * Provider packages.
	 * Multi-Provided is first
	 */
	public static array $PROVIDERS = [
		### BEGIN_PROVIDERS ###

### END_PROVIDERS ###
	];
	public static $DEPENDENCIES = [
		### BEGIN_DEPENDENCIES ###

### END_DEPENDENCIES ###
	];

	/**
	 * Get the URL for a module.
	 */
	public static function getGitUrl(string $moduleName, int $which = 1, bool $ssh = false): string
	{
		$git = self::GIT_PROVIDER;
		$which = (int)$which;
		$providers = self::getProviders($moduleName);
		$url = '';
		if (is_array($providers))
		{
			if (($which < 1) || ($which > count($providers)))
			{
				throw new GDO_Exception(
					'Invalid provider choice!');
			}
			$url = $git . $providers[$which - 1];
		}
		else
		{
			$url = $git . $providers;
		}

		if ($ssh)
		{
			$url = str_replace('https://', 'ssh://git@', $url);
		}

		return $url;
	}

	public static function getProviders(string $moduleName)
	{
		foreach (self::$PROVIDERS as $modname => $providers)
		{
			if (strcasecmp($moduleName, $modname) === 0)
			{
				return $providers;
			}
		}
		return null;
	}

	public static function getCleanModuleName(string $moduleName): string
	{
		foreach (array_keys(self::$PROVIDERS) as $modname)
		{
			if (strcasecmp($moduleName, $modname) === 0)
			{
				return $modname;
			}
		}
		throw new GDO_ExceptionFatal('err_unknown_module', [html($moduleName)]);
	}

	public static function getDependencies(string $moduleName): array
	{
		foreach (self::$DEPENDENCIES as $modname => $depNames)
		{
			if (strcasecmp($moduleName, $modname) === 0)
			{
				return $depNames;
			}
		}
		return GDT::EMPTY_ARRAY;
	}

	public static function getMultiProviders(): array
	{
		$back = [];
		foreach (self::$PROVIDERS as $modname => $providers)
		{
			if (is_array($providers))
			{
				$back[$modname] = $providers;
			}
		}
		return $back;
	}

	public static function isCoreModule(string $moduleName): bool
	{
		return in_array($moduleName, self::getCoreModuleNames(), true);
	}

	/**
	 * Get all core module names.
	 *
	 * @return string[]
	 */
	public static function getCoreModuleNames(): array
	{
		return ['Core', 'Date', 'Form', 'Language', 'UI', 'User'];
	}

}
