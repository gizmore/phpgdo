<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Date\GDO_Timezone;
use GDO\Date\Module_Date;
use GDO\Language\GDO_Language;
use GDO\Language\Module_Language;
use GDO\Language\Trans;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_User;

/**
 * The core module holds some generic config as well as the global revision string.
 * The first module by priority, and it *HAS* to be installed for db driven sites,
 *
 * Also this module provides the default theme,
 * which is almost empty and is using the default tpl of the modules.
 *
 * Very basic vanilla JS is optionally loaded.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
final class Module_Core extends GDO_Module
{

	final public const GDO_VERSION = '7.0.3';
	final public const GDO_REVISION = '7.0.3-r1820';

	final public const GDO_CODENAME = 'Jewish Jester';

	##############
	### Module ###
	##############
	public int $priority = 1;

	public function isCoreModule(): bool { return true; }

	public function getTheme(): ?string { return 'default'; }

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/core');
	}

	public function getClasses(): array
	{
		return [
			GDO_Hook::class,
			GDO_Module::class,
			GDO_ModuleVar::class,
			GDO_Language::class,
			GDO_Permission::class,
			GDO_Timezone::class,
			GDO_User::class,
			GDO_UserPermission::class,
			GDO_FileCache::class,
			GDO_SEO_URL::class,
		];
	}

	public function getDependencies(): array
	{
		return [
			'Crypto',
			'Date',
			'DBMS',
			'Form',
			'Language',
			'UI',
			'User',
		];
	}

	public function onInstall(): void
	{
		Install::onInstall($this);
	}

	public function checkSystemDependencies(): bool
	{
		$ok = true;
		if (PHP_MAJOR_VERSION < 8)
		{
			$ok = $this->errorSystemDependency('err_php_major_version', ['8.0']);
		}
		if (!function_exists('mime_content_type'))
		{
			$ok = $this->errorSystemDependency('err_php_extension', ['fileinfo']);
		}
		if (!function_exists('mb_strcut'))
		{
			$ok = $this->errorSystemDependency('err_php_extension', ['mbstring']);
		}
		if (!function_exists('iconv'))
		{
			$ok = $this->errorSystemDependency('err_php_extension', ['iconv']);
		}
		return $ok;
	}

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_User::make('system_user')->writeable(false)->initial('1'), # System user / id should be 1.
			GDT_Checkbox::make('show_impressum')->initial('1'), # show impressum in footer.
			GDT_Checkbox::make('show_privacy')->initial('1'), # show privacy link in footer.
			GDT_Checkbox::make('allow_guests')->initial('1'), # generally allow guests.
			GDT_Checkbox::make('allow_javascript')->initial('1'), # generally allow javascript.
			GDT_Version::make('asset_revision')->initial($this->version), # append this version to asset include urls?v=.
			GDT_Checkbox::make('siteshort_title_append')->initial('1'),
			GDT_Checkbox::make('mail_403')->initial('1'), # mail 403 error mails?
			GDT_Checkbox::make('mail_404')->initial('1'), # mail 404 error mails?
			GDT_Checkbox::make('directory_indexing')->initial('1'),
			GDT_Checkbox::make('module_assets')->initial('1'),
			GDT_Checkbox::make('dotfiles')->initial('0'),
		];
	}

    public function onInitSidebar(): void
	{
		$page = GDT_Page::instance();
		$bar = $page->bottomBar();
		if ($this->cfgShowImpressum())
		{
			$bar->addField(GDT_Link::make('impressum')
				->href(href('Core', 'Impressum'))->icon('legal'));
		}
		if ($this->cfgShowPrivacy())
		{
			$bar->addField(GDT_Link::make('privacy')
				->href(href('Core', 'Privacy'))->icon('info'));
		}
	}

	public function cfgShowImpressum(): string { return $this->getConfigVar('show_impressum'); }

	public function cfgShowPrivacy(): string { return $this->getConfigVar('show_privacy'); }

	public function onIncludeScripts(): void
	{
		$this->addCSS('css/gdo7.css');
		$this->addJS('js/gdo-string-util.js');
		$this->addJS('js/gdo-user.js');
		$this->addJS('js/gdo-core.js');
		Javascript::addJSPreInline($this->gdoConfigJS());
		Javascript::addJSPostInline($this->gdoUserJS());
	}

	/**
	 * Pretty print gdo config to JS.
	 */
	public function gdoConfigJS(): string
	{
		return sprintf(
			"	window.GDO_CONFIG = {};
	window.GDO_PROTOCOL = '%s';
	window.GDO_DOMAIN = '%s';
	window.GDO_PORT = '%s';
	window.GDO_WEB_ROOT = '%s';
	window.GDO_LANGUAGE = '%s';
	window.GDO_REVISION = '%s';
", GDO_PROTOCOL, GDO_DOMAIN, GDT_Url::port(),
			GDO_WEB_ROOT, Trans::$ISO,
			$this->nocacheVersion());
	}

	public function gdoUserJS(): string
	{
		$json = json($this->gdoUserJSON());
		return "window.GDO_USER = new GDO_User($json);";
	}

	public function gdoUserJSON(): array
	{
		$user = GDO_User::current();
		$data = $user->toJSON();
		$data['timezone'] = Module_Date::instance()->cfgUserTimezoneId($user);
		$data['language'] = Module_Language::instance()->cfgUserLangID($user);
		return $data;
	}

	public function cfgSystemUser(): GDO_User { return $this->getConfigValue('system_user'); }

	public function cfgSystemUserID(): string { return $this->getConfigVar('system_user'); }

	public function cfgAssetVersion(): Version { return $this->getConfigValue('asset_revision'); }

	public function cfgAllowGuests(): bool { return $this->getConfigValue('allow_guests'); }

	public function cfgAllowJavascript(): string { return $this->getConfigVar('allow_javascript'); }

	public function cfgSiteShortTitleAppend(): string { return $this->getConfigVar('siteshort_title_append'); }

	#############
	### Hooks ###
	#############

	public function cfgMail403(): string { return $this->getConfigVar('mail_403'); }

	##################
	### Javascript ###
	##################

	public function cfgMail404(): string { return $this->getConfigVar('mail_404'); }

	public function cfgDirectoryIndex(): string { return $this->getConfigVar('directory_indexing'); }

	public function cfgDotfiles(): bool { return $this->getConfigValue('dotfiles'); }

	/**
	 * Check if an url should be restricted due to GDO asset source restriction.
	 * You should enable this in production.
	 */
	public function checkAssetAllowed(string $url): bool
	{
		if ($this->cfgModuleAssets())
		{
			return true;
		}

		if (preg_match('/\\.(?:ttf|woff|woff2|png|gif|jpg|jpeg|svg)$/iD', $url))
		{
			return true;
		}

		return !str_starts_with($url, 'GDO/');
	}

	public function cfgModuleAssets(): string { return $this->getConfigVar('module_assets'); }

}
