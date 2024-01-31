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
'EdwardSnowdenLand' => 'phpgdo-esl',
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
'GDO6DB' => 'phpgdo-gdo6',
'Geo2City' => 'phpgdo-geo2city',
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
'Maintenance' => 'phpgdo-maintenance',
'Maps' => 'phpgdo-maps',
'Markdown' => 'phpgdo-markdown',
'Math' => 'phpgdo-math',
'Mettwitze' => 'phpgdo-mettwitze',
'Mibbit' => 'phpgdo-mibbit',
'Moment' => 'phpgdo-moment',
'News' => 'phpgdo-news',
'OnlineUsers' => 'phpgdo-online-users',
'OnSocial' => 'phpgdo-on-social',
'OpenTimes' => 'phpgdo-open-times',
'Payment' => 'phpgdo-payment',
'PaymentBank' => 'phpgdo-payment-bank',
'PaymentCredits' => 'phpgdo-payment-credits',
'PaymentPaypal' => 'phpgdo-payment-paypal',
'PaypalDonations' => 'phpgdo-paypal-donations',
'PDFParser' => 'phpgdo-pdf-parser',
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
'TesseractOCR' => 'phpgdo-tesseract-ocr',
'Todo' => 'phpgdo-todo',
'TorChallenge' => 'phpgdo-tor-challenge',
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

Warning: Undefined array key "REMOTE_ADDR" in D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Warning: Cannot modify header information - headers already sent by (output started at D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in D:\ProjektPHPGDO\phpgdo\GDO7.php on line 345
<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Undefined array key "REMOTE_ADDR"&nbsp;in&nbsp;<b style=/"font-size:16px;/">D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">181</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - GDO\Core\Logger::logException(ParseError) ............................................................................................................... D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 317.
 - GDO\Core\Logger::log(&quot;critical&quot;, &quot;syntax error, unexpected token \&quot;}\&quot; in D:/ProjektPHPGDO/phpgdo/GDO/TorChallenge/Module_TorChalleng…php Line 35\n&quot;, 8)  D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181.</pre>

Warning: Undefined array key "REMOTE_ADDR" in D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Warning: Cannot modify header information - headers already sent by (output started at D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in D:\ProjektPHPGDO\phpgdo\GDO7.php on line 345
<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Undefined array key "REMOTE_ADDR"&nbsp;in&nbsp;<b style=/"font-size:16px;/">D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">181</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - GDO\Core\Logger::logException(ParseError) ............................................................................................................... D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 319.
 - GDO\Core\Logger::log(&quot;critical&quot;, &quot;&amp;lt;span style=\&quot;color: red;\&quot;&amp;gt;ParseError&amp;lt;/span&amp;gt;: \u00b4&amp;lt;i&amp;gt;syntax error, unexpected … line 35.\r\n&quot;, 8)  D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181.</pre>

Warning: Undefined array key "REMOTE_ADDR" in D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Warning: Cannot modify header information - headers already sent by (output started at D:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in D:\ProjektPHPGDO\phpgdo\GDO7.php on line 345
<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Cannot modify header information - headers already sent by (output started at D:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php:181)&nbsp;in&nbsp;<b style=/"font-size:16px;/">D:/ProjektPHPGDO/phpgdo/GDO7.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">345</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - hdrc(&quot;HTTP/1.1 500 Server Error&quot;) ........ D:/ProjektPHPGDO/phpgdo/GDO7.php line 324.
 - hdr(&quot;HTTP/1.1 500 Server Error&quot;, true) ... D:/ProjektPHPGDO/phpgdo/GDO7.php line 345.
 - header(&quot;HTTP/1.1 500 Server Error&quot;, true)  [unknown file] line ?.</pre>
<div class="gdo-exception">
<em><span style="color: red;">ParseError</span>: ´<i>syntax error, unexpected token "}"</i>´ in <b>D:/ProjektPHPGDO/phpgdo/GDO/TorChallenge/Module_TorChallenge.php</b> line <b>35</b></em><div class="gdt-hr"></div><pre>Backtrace starts in D:/ProjektPHPGDO/phpgdo/provider_dependencies.php line 26.
 - GDO\Core\ModuleLoader-&gt;loadModules(false, true, true) ................................................................. D:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 277.
 - GDO\Core\ModuleLoader-&gt;loadModulesFS(false) ........................................................................... D:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 353.
 - GDO\Util\Filewalker::traverse(&quot;D:\ProjektPHPGDO\phpgdo/GDO/&quot;, NULL, NULL, [
    {},
    &quot;_loadModuleFS&quot;
], , false) ... D:/ProjektPHPGDO/phpgdo/GDO/Util/Filewalker.php line 24.
 - gizmore\Filewalker::traverse(&quot;D:\ProjektPHPGDO\phpgdo/GDO&quot;, NULL, NULL, [
    {},
    &quot;_loadModuleFS&quot;
], , false, &quot;\&quot;)  D:/ProjektPHPGDO/phpgdo/GDO/Util/php-filewalker/gizmore/Filewalker.php line 104.
 - call_user_func([
    {},
    &quot;_loadModuleFS&quot;
], &quot;TorChallenge&quot;, &quot;D:\ProjektPHPGDO\phpgdo/GDO\TorChallenge&quot;, false) .... [unknown file] line ?.
 - GDO\Core\ModuleLoader-&gt;_loadModuleFS(&quot;TorChallenge&quot;, &quot;D:\ProjektPHPGDO\phpgdo/GDO\TorChallenge&quot;, false) ............... D:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 506.
 - GDO\Core\ModuleLoader-&gt;loadModuleFS(&quot;TorChallenge&quot;) ................................................................... D:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 187.
 - class_exists(&quot;GDO\TorChallenge\Module_TorChallenge&quot;) .................................................................. [unknown file] line ?.
 - {closure}(&quot;GDO/TorChallenge/Module_TorChallenge&quot;) ..................................................................... D:/ProjektPHPGDO/phpgdo/GDO/TorChallenge/Module_TorChallenge.php line 35.</pre>
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
