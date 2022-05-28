<?php
namespace GDO\Core;

/**
 * Official registered gdo6 module mapping.
 * Installer can spit out repo urls for modules.
 * Some modules have multiple providers, like gdo6-session-db and gdo6-session-cookie.
 * Both provide Module_Session.
 *
 * You can generate providers and dependenices with providers.php and provider_dependenciews.php
 *
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.0
 */
final class ModuleProviders
{
	const GIT_PROVIDER = 'https://github.com/gizmore/';

	/**
	 * Get URL for a module.
	 *
	 * @param string $moduleName
	 * @param number $which
	 * @return string
	 */
	public static function getGitUrl($moduleName, $which = 1)
	{
		$git = self::GIT_PROVIDER;
		$which = (int) $which;
		$providers = self::getProviders($moduleName);
		if (is_array($providers))
		{
			if (($which < 1) || ($which > count($providers)))
			{
				throw new GDO_Exception(
					"Invalid provider choice!");
			}
			return $git . $providers[$which - 1];
		}
		return $git . $providers;
	}
	
	public static function getDependencies(string $moduleName) : ?array
	{
		foreach (self::$DEPENDENCIES as $modname => $depNames)
		{
			if (strcasecmp($moduleName, $modname) === 0)
			{
				return $depNames;
			}
		}
		return null;
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
	
	public static $PROVIDERS = [
		'Captcha' => [
			'phpgdo-captcha',
			'phpgdo-recaptcha2'
		],
		'DBMS' => [
			'phpgdo-mysql',
			'phpgdo-postgres',
			'phpgdo-sqlite'
		],
		'Mailer' => [
			'phpgdo-mailer-gdo',
			'phpgdo-mailer-symfony'
		],
		'Session' => [
			'phpgdo-session-db',
			'phpgdo-session-cookie'
		],
		'Bootstrap5' => 'phpgdo-bootstrap5',
		'Bootstrap5Theme' => 'phpgdo-bootstrap5-theme',
		'Classic' => 'phpgdo-classic',
		'Country' => 'phpgdo-country',
		'CSS' => 'phpgdo-css',
		'DOMPDF' => 'phpgdo-dompdf',
		'File' => 'phpgdo-file',
		'Javascript' => 'phpgdo-javascript',
		'JQuery' => 'phpgdo-jquery',
		'JQueryAutocomplete' => 'phpgdo-jquery-autocomplete',
		'Login' => 'phpgdo-login',
		'Mail' => 'phpgdo-mail',
		'Mailer' => 'phpgdo-mailer-gdo',
	];

	public static $DEPENDENCIES = [
		'Admin' => [
			'Table'
		],
		'Bootstrap5' => [
			'Core',
			'JQuery'
		],
		'Bootstrap5Theme' => [
			'Bootstrap5'
		],
		'Captcha' => [],
		'Classic' => [],
		'CLI' => [],
		'Core' => [
			'Language',
			'Date',
			'UI',
			'User'
		],
		'Country' => [],
		'Cronjob' => [],
		'Crypto' => [],
		'CSS' => [],
		'Date' => [],
		'DOMPDF' => [
			'File'
		],
		'File' => [],
		'Git' => [],
		'Gitwatch' => [
			'Git',
			'Bootstrap5Theme',
			'Session'
		],
		'Install' => [],
		'Javascript' => [],
		'JQuery' => [],
		'JQueryAutocomplete' => [
			'JQuery'
		],
		'Language' => [],
		'Login' => [
			'Session'
		],
		'Mail' => [
			'User',
			'Mailer'
		],
		'Mailer' => [],
		'Net' => [],
		'Perf' => [],
		'PHPInfo' => [],
		'Realname' => [],
		'Session' => [],
		'Table' => [],
		'Tests' => [],
		'UI' => [],
		'User' => [
			'Core'
		],
	];
	
}
    