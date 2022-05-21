<?php
namespace GDO\Core;

use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\User\GDT_User;
use GDO\Language\Trans;
use GDO\User\GDO_Permission;
use GDO\UI\GDT_Page;
use GDO\Util\FileUtil;
use GDO\User\GDO_UserPermission;
use GDO\Date\GDO_Timezone;
use GDO\Net\GDT_Url;
use phpDocumentor\Reflection\PseudoTypes\True_;

/**
 * The core module holds some generic config as well as the global revision string.
 * The first module by priority, and it *HAS* to be installed for db driven sites,
 * simply because it installs the module table.
 *
 * Also this module provides the default theme,
 * which is almost empty and is using the default tpl of the modules.
 *
 * Very basic vanilla JS is loaded.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see Module_Javascript
 */
final class Module_Core extends GDO_Module
{
	/**
	 * GDO Revision String.
	 * Counts up to be in sync and poison caches for updates.
	 * Increase this value to poison all caches.
	 */
	const GDO_REVISION = '7.0.1-r1029';
	
	##############
	### Module ###
	##############
	public int $priority = 1;
	
	public function isCoreModule() : bool { return true; }
	
	public function getTheme() : string { return 'default'; }
	
	public function onLoadLanguage() : void { $this->loadLanguage('lang/core'); }
	
	public function thirdPartyFolders() : array { return ['/htmlpurifier/']; }
	
	public function getClasses() : array
	{
		return [
			GDO_Hook::class,
			GDO_Module::class,
			GDO_ModuleVar::class,
			GDO_Permission::class,
			GDO_Timezone::class,
			GDO_User::class,
			GDO_UserPermission::class,
		];
	}
	
	public function getDependencies() : array
	{
		return ['User'];
	}
	
	public function onInstall() : void
	{
		FileUtil::createDir(GDO_PATH.'assets');
		FileUtil::createDir(GDO_TEMP_PATH);
		FileUtil::createDir(GDO_TEMP_PATH.'cache');
		FileUtil::createFile(GDO_TEMP_PATH.'ipc.socket');
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_User::make('system_user')->writeable(false)->initial('1'), # System user / id should be 1.
			GDT_Checkbox::make('show_impressum')->initial('0'), # show impressum in footer.
			GDT_Checkbox::make('show_privacy')->initial('0'), # show privacy link in footer.
			GDT_Checkbox::make('allow_guests')->initial('1'), # generally allow guests.
			GDT_Version::make('asset_revision')->initial($this->version), # append this version to asset include urls?v=.
			GDT_Checkbox::make('siteshort_title_append')->initial('1'),
			GDT_Checkbox::make('mail_403')->initial('1'), # mail 403 error mails?
			GDT_Checkbox::make('mail_404')->initial('1'), # mail 404 error mails?
			GDT_Checkbox::make('load_sidebars')->initial('1'),
			GDT_Checkbox::make('directory_indexing')->initial('0'),
			GDT_Checkbox::make('module_assets')->initial('1'),
		];
	}
	
	public function cfgSystemUser() : GDO_User { return $this->getConfigValue('system_user'); }
	public function cfgSystemUserID() : string { return $this->getConfigVar('system_user'); }
	public function cfgShowImpressum() : string { return $this->getConfigVar('show_impressum'); }
	public function cfgShowPrivacy() : string { return $this->getConfigVar('show_privacy'); }
	public function cfgAssetVersion() : Version { return $this->getConfigValue('asset_revision'); }
	public function cfgAllowGuests() : string { return $this->getConfigVar('allow_guests'); }
	public function cfgSiteShortTitleAppend() : string { return $this->getConfigVar('siteshort_title_append'); }
	public function cfgMail403() : string { return $this->getConfigVar('mail_404'); }
	public function cfgMail404() : string { return $this->getConfigVar('mail_404'); }
	public function cfgLoadSidebars() : string { return $this->getConfigVar('load_sidebars'); }
	public function cfgDirectoryIndex() : string { return $this->getConfigVar('directory_indexing'); }
	public function cfgModuleAssets() : string { return $this->getConfigVar('module_assets'); }
	
	#############
	### Hooks ###
	#############
	public function onInitSidebar() : void
	{
		$page = GDT_Page::instance();
		if ($this->cfgShowImpressum())
		{
			$page->leftBar()->addField(GDT_Link::make('link_impressum')->href(href('Core', 'Impressum')));
		}
		if ($this->cfgShowPrivacy())
		{
			$page->leftBar()->addField(GDT_Link::make('link_privacy')->href(href('Core', 'Privacy')));
		}
	}
	
	public function hookIgnoreDocsFiles(GDT_Array $ignore)
	{
		$ignore->data[] = 'GDO/UI/htmlpurifier/**/*';
	}
	
	##################
	### Javascript ###
	##################
	public function onIncludeScripts() : void
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
	 * @return string
	 */
	public function gdoConfigJS()
	{
		return sprintf(
		"window.GDO_CONFIG = {};
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
	
	public function gdoUserJS()
	{
		$json = json_encode($this->gdoUserJSON(), JSON_PRETTY_PRINT);
		return "window.GDO_USER = new GDO_User($json);";
	}
	
	public function gdoUserJSON()
	{
		$user = GDO_User::current();
		return $user->toJSON();
	}
	
	/**
	 * Check if an url should be restricted due to GDO asset source restriction.
	 * You should enable this in production.
	 */
	public function checkAssetAllowed(string $url) : bool
	{
		return $this->cfgModuleAssets() ?
			true : 
			(strpos($url, 'GDO/') === false);
	}
	
}
