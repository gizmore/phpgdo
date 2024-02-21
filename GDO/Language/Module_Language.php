<?php
declare(strict_types=1);
namespace GDO\Language;

use GDO\Core\Application;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Enum;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_Template;
use GDO\Core\Javascript;
use GDO\Core\Website;
use GDO\Language\Method\SwitchLang;
use GDO\Net\GDT_Url;
use GDO\Session\GDO_Session;
use GDO\UI\GDT_Divider;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;
use GDO\Util\Strings;

/**
 * Selfmade cheap Internationalization Module.
 *
 * - Detect language by cookie, parameter or http_accept_language
 *
 * - Provide lang switcher via cookie
 * - Provide language select
 * - Provide GDO_Language table
 * - Provide Trans for i18n.
 *
 * @version 7.0.3
 * @since 2.0.0
 * @author gizmore
 * @see Trans
 * @see GDO_Language
 *
 */
final class Module_Language extends GDO_Module
{

	##############
	### Module ###
	##############
	public int $priority = 2; # has to be installed asap.

	public function isCoreModule(): bool { return true; }

	public function onInstall(): void { LanguageData::onInstall(); }

	public function onLoadLanguage(): void { $this->loadLanguage('lang/language'); }

	##############
	### Config ###
	##############
	public function getConfig(): array
	{
		return [
			GDT_Language::make('languages')->all()->multiple()->initial('["' . GDO_LANGUAGE . '"]'),
			GDT_EnumNoI18n::make('langswitch_left')->enumValues('large', 'flags', 'none')->initial('flags'),
			GDT_Checkbox::make('use_in_javascript')->initial('1'),
		];
	}

	public function getACLDefaults(): array
	{
		return [
			'language' => ['acl_all', '0', null],
		];
	}

	public function getUserSettings(): array
	{
		return [
			GDT_Language::make('language')->withCompletion(),
		];
	}

	################
	### Settings ###
	################

	/**
	 * Add meta tag on init.
	 */
	public function onModuleInit(): void
	{
		Website::addMeta(['language', Trans::$ISO, 'name']);
	}

	public function onInitSidebar(): void
	{
        switch ($this->cfgSwitchLeft())
        {
            case 'large':
                $navbar = GDT_Page::instance()->leftBar();
                $navbar->addField(GDT_Method::make()->method(SwitchLang::make()));
                $navbar->addField(GDT_Divider::make());
                break;
            case 'flags':
                $navbar = GDT_Page::instance()->leftBar();
                $navbar->addField(GDT_Template::make()->template('Language', 'langswitch_html.php'));
                $navbar->addField(GDT_Divider::make());
                break;
        }
	}

	public function cfgSwitchLeft(): string { return $this->getConfigVar('langswitch_left'); }

	#################
	### Supported ###
	#################

	public function onIncludeScripts(): void
	{
		# If enabled include js trans data and translation engine.
		if ($this->cfgJavascript())
		{
			# Add js trans
			$href = sprintf(
				'%sindex.php?_mo=Language&_me=GetTransData&_lang=%s&_ajax=1&_fmt=html&%s',
				GDO_WEB_ROOT, Trans::$ISO, $this->nocacheVersion());
			$href = GDT_Url::absolute($href);
			Javascript::addJS($href);

			# Add cheap js translation engine.
			$this->addJS('js/gdo-trans.js');
		}
	}

	public function cfgJavascript(): bool { return $this->getConfigValue('use_in_javascript'); }

	############
	### Init ###
	############

	public function cfgUserLangID(GDO_User $user): ?string
	{
		return $this->userSettingVar($user, 'language');
	}

	/**
	 * Get the supported  languages, GDO_LANGUAGE first.
	 * @return GDO_Language[]
	 */
	public function cfgSupported(): array
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

	#################
	### Detection ###
	#################

	/**
	 * This is the language detection method you are looking for.
	 */
	public function detectISO(): string
	{
		if (isset($_REQUEST['_lang']))
		{
			$iso = (string)@$_REQUEST['_lang'];
			unset($_REQUEST['_lang']);
			if ($this->isISOSupported($iso))
			{
				return $iso;
			}
		}
		if (Application::instance()->hasSession())
		{
			if ($iso = GDO_Session::get('gdo-language'))
			{
				return $iso;
			}
		}
		if ($iso = GDO_User::current()->getLangISO())
		{
			return $iso;
		}
		if ($iso = $this->detectAcceptLanguage())
		{
			return $iso;
		}
		return GDO_LANGUAGE;
	}

	public function detectAcceptLanguage(): string
	{
		$matches = [];
		$languages = GDO_Language::table()->allSupported();
		if (preg_match_all('/[-a-zA-Z,]+;q=[.\d]+/', (string)@$_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches))
		{
			foreach ($matches[0] as $match)
			{
				[$isos] = explode(';', ltrim($match, ','));
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
		return GDO_LANGUAGE;
	}

	private function isISOSupported(string $iso): bool
	{
		$langs = $this->cfgSupported();
		return isset($langs[$iso]);
	}

}
