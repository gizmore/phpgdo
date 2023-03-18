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
 * @version 7.0.1
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
	public static function getGitUrl(string $moduleName, int $which = 1, bool $ssh=false)
	{
		$git = self::GIT_PROVIDER;
		$which = (int) $which;
		$providers = self::getProviders($moduleName);
		$url = '';
		if (is_array($providers))
		{
			if (($which < 1) || ($which > count($providers)))
			{
				throw new GDO_Exception(
					"Invalid provider choice!");
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

	public static function getCleanModuleName(string $moduleName): string
	{
		foreach (array_keys(self::$PROVIDERS) as $modname)
		{
			if (strcasecmp($moduleName, $modname) === 0)
			{
				return $modname;
			}
		}
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
	
	public static function getMultiProviders() : array
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
	
	/**
	 * Get all core module names.
	 * 
	 * @return string[]
	 */
	public static function getCoreModuleNames() : array
	{
		return ['Core', 'Date', 'Form', 'Language', 'UI', 'User'];
	}
	
	public static function isCoreModule(string $moduleName) : bool
	{
		return in_array($moduleName, self::getCoreModuleNames(), true);
	}

	/**
	 * Provider packages.
	 * Multi-Provided is first
	 */
	public static array $PROVIDERS = [
		### BEGIN_PROVIDERS ###
'Captcha' => ['phpgdo-captcha', 'phpgdo-recaptcha2'],
'DBMS' => ['phpgdo-mysql', 'phpgdo-postgres', 'phpgdo-sqlite'],
'Mailer' => ['phpgdo-mailer', 'phpgdo-mailer-symfony'],
'Session' => ['phpgdo-session-db', 'phpgdo-session-cookie'],
'AboutMe' => 'phpgdo-about-me',
'Account' => 'phpgdo-account',
'ACME' => 'phpgdo-acme',
'ActivationAlert' => 'phpgdo-activation-alert',
'Address' => 'phpgdo-address',
'Ads' => 'phpgdo-ads',
'Aprilfools' => 'phpgdo-aprilfools',
'Avatar' => 'phpgdo-avatar',
'Backup' => 'phpgdo-backup',
'Ban' => 'phpgdo-ban',
'BasicAuth' => 'phpgdo-basic-auth',
'Birthday' => 'phpgdo-birthday',
'Bootstrap5' => 'phpgdo-bootstrap5',
'Bootstrap5Theme' => 'phpgdo-bootstrap5-theme',
'Category' => 'phpgdo-category',
'ChatGPT' => 'phpgdo-chatgpt',
'CKEditor' => 'phpgdo-ck-editor',
'Classic' => 'phpgdo-classic',
'Codinggeex' => 'phpgdo-codinggeex',
'Comments' => 'phpgdo-comments',
'Contact' => 'phpgdo-contact',
'CORS' => 'phpgdo-cors',
'Country' => 'phpgdo-country',
'CountryCoordinates' => 'phpgdo-country-coordinates',
'CountryRestrictions' => 'phpgdo-country-restrictions',
'CSS' => 'phpgdo-css',
'Currency' => 'phpgdo-currency',
'Diary' => 'phpgdo-diary',
'Dog' => 'gdo6-dog',
'DogAuth' => 'gdo6-dog-auth',
'DogBlackjack' => 'gdo6-dog-blackjack',
'DogChatGPT' => 'phpgdo-dog-chatgpt',
'DogGreetings' => 'gdo6-dog-greetings',
'DogIRC' => 'gdo6-dog-irc',
'DogIRCAutologin' => 'gdo6-dog-irc-autologin',
'DogIRCSpider' => 'gdo6-dog-irc-spider',
'DogShadowdogs' => 'gdo6-dog-shadowdogs',
'DogTeams' => 'phpgdo-dog-teams',
'DogTick' => 'gdo6-dog-tick',
'DogWebsite' => 'gdo6-dog-website',
'DOMPDF' => 'phpgdo-dompdf',
'DoubleAccounts' => 'phpgdo-double-accounts',
'Download' => 'phpgdo-download',
'DSGVO' => 'phpgdo-dsgvo',
'Facebook' => 'phpgdo-facebook',
'Favicon' => 'phpgdo-favicon',
'FFMpeg' => 'phpgdo-ffmpeg',
'File' => 'phpgdo-file',
'Fineprint' => 'phpgdo-fineprint',
'Follower' => 'phpgdo-follower',
'FontAtkinson' => 'phpgdo-font-atkinson',
'FontAwesome' => 'phpgdo-font-awesome',
'Forum' => 'phpgdo-forum',
'Friends' => 'phpgdo-friends',
'Gallery' => 'phpgdo-gallery',
'Geo2Country' => 'phpgdo-geo2country',
'GTranslate' => 'phpgdo-gtranslate',
'Guestbook' => 'phpgdo-guestbook',
'Hash' => 'phpgdo-hash',
'Helpdesk' => 'phpgdo-helpdesk',
'HTML' => 'phpgdo-html',
'Hydra' => 'phpgdo-hydra',
'ImageMagick' => 'phpgdo-image-magick',
'Instagram' => 'phpgdo-instagram',
'Invite' => 'phpgdo-invite',
'IP2Country' => 'phpgdo-ip2country',
'ITMB' => '',
'Javascript' => 'phpgdo-javascript',
'JPGraph' => 'phpgdo-jpgraph',
'JQuery' => 'phpgdo-jquery',
'JQueryAutocomplete' => 'phpgdo-jquery-autocomplete',
'KassiererCard' => 'phpgdo-kassierer-card',
'Licenses' => 'phpgdo-licenses',
'Links' => 'phpgdo-links',
'LinkUUp' => '',
'LoC' => 'phpgdo-loc',
'Login' => 'phpgdo-login',
'Mail' => 'phpgdo-mail',
'Mailer' => 'phpgdo-mailer',
'Maintenance' => 'phpgdo-maintenance',
'Maps' => 'phpgdo-maps',
'Markdown' => 'phpgdo-markdown',
'Math' => 'phpgdo-math',
'Mettwitze' => 'phpgdo-mettwitze',
'Mibbit' => 'phpgdo-mibbit',
'Moment' => 'phpgdo-moment',
'News' => 'phpgdo-news',
'OnlineUsers' => 'phpgdo-online-users',
'OpenTimes' => 'phpgdo-open-times',
'Payment' => 'phpgdo-payment',
'PaymentBank' => 'phpgdo-payment-bank',
'PaymentCredits' => 'phpgdo-payment-credits',
'PaymentPaypal' => 'phpgdo-payment-paypal',
'PaypalDonations' => 'phpgdo-paypal-donations',
'PHPGDO' => 'phpgdo-phpgdo-website',
'PM' => 'phpgdo-pm',
'PMA' => 'phpgdo-pma',
'Poll' => 'phpgdo-poll',
'Prism' => 'phpgdo-prism',
'Python' => 'phpgdo-python',
'QRCode' => 'phpgdo-qrcode',
'Quotes' => 'phpgdo-quotes',
'Recalcolo' => '',
'Recovery' => 'phpgdo-recovery',
'Register' => 'phpgdo-register',
'Security' => 'phpgdo-security',
'Shadowlamb' => 'phpgdo-shadowlamb',
'Shoutbox' => 'phpgdo-shoutbox',
'SimpleMDE' => 'phpgdo-simple-mde',
'Sitemap' => 'phpgdo-sitemap',
'Statistics' => 'phpgdo-statistics',
'Tags' => 'phpgdo-tags',
'TBS' => 'phpgdo-tbs',
'TCPDF' => 'phpgdo-tcpdf',
'Todo' => 'phpgdo-todo',
'TorDetection' => 'phpgdo-tor-detection',
'Tradestation' => '',
'Votes' => 'phpgdo-votes',
'VPNDetect' => 'phpgdo-vpn-detect',
'Websocket' => 'phpgdo-websocket',
'YouTube' => 'phpgdo-youtube',
'YTBest' => 'phpgdo-ytbest',
'ZIP' => 'phpgdo-zip',
### END_PROVIDERS ###
	];

	public static $DEPENDENCIES = [
		### BEGIN_DEPENDENCIES ###
<div class="gdo-exception">
<em>GDO/Core/GDO_Error: ´<i>err_module: ["Codinggeex"]</i>´ in <b>C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php</b> line <b>385</b></em><div class="gdt-hr"></div><pre>Backtrace starts in C:/ProjektPHPGDO/phpgdo/provider_dependencies.php line 25.
 - GDO\Core\ModuleLoader-&gt;loadModules(false, true, true) ....................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 252.
 - GDO\Core\ModuleLoader-&gt;loadModulesFS(false) ................................................................. C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 331.
 - GDO\Util\Filewalker::traverse(&quot;C:\ProjektPHPGDO\phpgdo/GDO/&quot;, NULL, NULL, [{},&quot;_loadModuleFS&quot;], 0, false) ... C:/ProjektPHPGDO/phpgdo/GDO/Util/Filewalker.php line 25.
 - gizmore\Filewalker::traverse(&quot;C:\ProjektPHPGDO\phpgdo/GDO&quot;, NULL, NULL, [{},&quot;_loadModuleFS&quot;], 0, false, &quot;\&quot;)  C:/ProjektPHPGDO/phpgdo/GDO/Util/php-filewalker/gizmore/Filewalker.php line 104.
 - call_user_func([{},&quot;_loadModuleFS&quot;], &quot;Codinggeex&quot;, &quot;C:\ProjektPHPGDO\phpgdo/GDO\Codinggeex&quot;, false) ......... [unknown file] line ?.
 - GDO\Core\ModuleLoader-&gt;_loadModuleFS(&quot;Codinggeex&quot;, &quot;C:\ProjektPHPGDO\phpgdo/GDO\Codinggeex&quot;, false) ......... C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 350.
 - GDO\Core\ModuleLoader-&gt;loadModuleFS(&quot;Codinggeex&quot;, true, false) .............................................. C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 385.</pre>
### END_DEPENDENCIES ###
	];

}
    