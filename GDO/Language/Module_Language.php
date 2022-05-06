<?php
namespace GDO\Language;

use GDO\Core\GDO_Module;
use GDO\Util\Strings;
use GDO\Core\Application;
use GDO\User\GDO_User;
use GDO\Session\GDO_Session;
use GDO\Core\Website;
use GDO\UI\GDT_Divider;
use GDO\Core\GDT_Checkbox;
use GDO\Javascript\Javascript;
use GDO\UI\GDT_Page;
use GDO\Net\GDT_Url;

/**
 * Internationalization Module.
 * 
 * - Detect language by cookie or http_accept_language
 * 
 * - Provide lang switcher via cookie
 * - Provide language select
 * - Provide GDO_Language table
 * - Provide Trans for i18n.
 * 
 * @see Trans
 * @see GDO_Language
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 2.0.0
 */
final class Module_Language extends GDO_Module
{
	public int $priority = 2;
	
	public function getClasses() : array
	{
		return [
			GDO_Language::class,
		];
	}

	public function onInstall() : void { LanguageData::onInstall(); }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/language'); }

	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Language::make('languages')->all()->multiple()->initial('["'.GDO_LANGUAGE.'"]'),
		    GDT_Checkbox::make('langswitch_left')->initial('1'),
		    GDT_Checkbox::make('use_in_javascript')->initial('1'),
		];
	}
	
	public function cfgSwitchLeft() : string { return $this->getConfigVar('langswitch_left'); }
	public function cfgJavascript() : string { return $this->getConfigVar('use_in_javascript'); }
	
	/**
	 * Get the supported  languages, GDO_LANGUAGE first.
	 * @return GDO_Language[]
	 */
	public function cfgSupported() : array
	{
		$supported = [GDO_LANGUAGE => GDO_Language::table()->find(GDO_LANGUAGE)];
		if ($additional = $this->getConfigValue('languages'))
		{
			foreach ($additional as $lang)
			{
				$supported[$lang->getISO()] = $lang;
			}
		}
		return $supported;
	}
	
	############
	### Init ###
	############
	public function onInit() : void
	{
	    $iso = $this->detectISO();
	    Trans::setISO($iso);
	    if (!Application::instance()->isCLI())
	    {
	        Website::addMeta(['language', $iso, 'name']);
	    }
	}
	
	public function onInitSidebar() : void
	{
		if ($this->cfgSwitchLeft())
		{
		    $navbar = GDT_Page::$INSTANCE->leftNav;
		    $navbar->addField(GDT_LangSwitch::make());
		    $navbar->addField(GDT_Divider::make());
		}
	}
	
	public function onIncludeScripts() : void
	{
	    # If enabled include js trans data and translation engine.
	    if ($this->cfgJavascript())
	    {
    		# Add js trans
    		$href = sprintf(
    			'%sindex.php?mo=Language&me=GetTransData&_lang=%s&_ajax=1&_fmt=html&%s',
    			GDO_WEB_ROOT, Trans::$ISO, $this->nocacheVersion());
    		$href = GDT_Url::absolute($href);
    		Javascript::addJS($href);
    
    		# Add cheap translation engine.
    		$this->addJS('js/gdo-trans.js');
	    }
	}
	
	#################
	### Detection ###
	#################
	public function detectISO() : string
	{
		if ($iso = (string) @$_REQUEST['_lang'])
		{
			return $iso;
		}
		if ($iso = GDO_Session::get('gdo-language'))
		{
			return $iso;
		}
		if ($iso = $this->detectAcceptLanguage())
		{
			return $iso;
		}
		if ($iso = GDO_User::current()->getLangISO())
		{
			return $iso;
		}
		return GDO_LANGUAGE;
	}
	
	public function detectAcceptLanguage() : string
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$matches = [];
			$languages = GDO_Language::table()->allSupported();
			if (preg_match_all("/[-a-zA-Z,]+;q=[.\d]+/", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches))
			{
				foreach ($matches[0] as $match)
				{
					list($isos) = explode(';', ltrim($match, ','));
					foreach (explode(',', $isos) as $iso)
					{
						$iso = strtolower(Strings::substrTo($iso, '-', $iso));
						if (isset($languages[$iso]))
						{
							return $iso;
						}
					}
				}
			}
		}
	}
	
}
