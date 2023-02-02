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
'CKEditor' => 'phpgdo-ck-editor',
'Classic' => 'phpgdo-classic',
'Comments' => 'phpgdo-comments',
'Contact' => 'phpgdo-contact',
'CORS' => 'phpgdo-cors',
'Country' => 'phpgdo-country',
'CountryCoordinates' => 'phpgdo-country-coordinates',
'CountryRestrictions' => 'phpgdo-country-restrictions',
'CSS' => 'phpgdo-css',
'Currency' => 'phpgdo-currency',
'Dog' => 'gdo6-dog',
'DogAuth' => 'gdo6-dog-auth',
'DogBlackjack' => 'gdo6-dog-blackjack',
'DogGreetings' => 'gdo6-dog-greetings',
'DogIRC' => 'gdo6-dog-irc',
'DogIRCAutologin' => 'gdo6-dog-irc-autologin',
'DogIRCSpider' => 'gdo6-dog-irc-spider',
'DogShadowdogs' => 'gdo6-dog-shadowdogs',
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
'AboutMe' => ['Account'],
'Account' => ['Login'],
'ActivationAlert' => [],
'Address' => ['Mail', 'Country'],
'Admin' => ['Table'],
'Ads' => ['Payment'],
'Aprilfools' => [],
'Avatar' => ['File'],
'Backup' => ['ZIP', 'Cronjob'],
'BasicAuth' => [],
'Birthday' => [],
'Bootstrap5' => ['Core', 'JQuery'],
'Bootstrap5Theme' => ['Bootstrap5', 'Moment'],
'Captcha' => [],
'Category' => [],
'CKEditor' => ['HTML', 'JQuery'],
'Classic' => [],
'CLI' => [],
'Comments' => ['Votes', 'File'],
'Contact' => ['Mail'],
'Core' => ['Language', 'Crypto', 'Date', 'UI', 'User', 'Form'],
'CORS' => [],
'Country' => [],
'CountryCoordinates' => ['Country', 'Maps'],
'CountryRestrictions' => [],
'Cronjob' => [],
'Crypto' => [],
'CSS' => [],
'Currency' => ['Cronjob'],
'Date' => [],
'DBMS' => [],
'Dog' => ['Cronjob'],
'DogAuth' => ['Dog'],
'DogBlackjack' => [],
'DogGreetings' => ['Dog'],
'DogIRC' => ['DogAuth'],
'DogIRCAutologin' => ['DogAuth', 'DogIRC'],
'DogIRCSpider' => ['DogIRC'],
'DogShadowdogs' => ['DogAuth'],
'DogTick' => ['Dog', 'DogIRC'],
'DogWebsite' => ['Bootstrap5Theme', 'JQuery', 'Avatar', 'Dog', 'DogAuth', 'Login', 'Register', 'Admin', 'DogIRC', 'DogTick', 'DogShadowdogs', 'DogIRCAutologin', 'DogIRCSpider', 'DogGreetings', 'DogBlackjack', 'News', 'PM', 'Quotes', 'Shoutbox', 'Forum', 'Links', 'Download', 'Math', 'Contact', 'Todo', 'Perf', 'Markdown'],
'DOMPDF' => ['File'],
'DoubleAccounts' => [],
'Download' => ['Payment'],
'DSGVO' => [],
'Facebook' => [],
'Favicon' => [],
'FFMpeg' => ['CLI'],
'File' => ['Session'],
'Fineprint' => ['Admin', 'Classic', 'Contact', 'CSS', 'DOMPDF', 'Javascript', 'JQueryAutocomplete', 'Licenses', 'Login', 'Perf'],
'Follower' => [],
'FontAtkinson' => [],
'FontAwesome' => [],
'Form' => [],
'Forum' => ['File'],
'Friends' => [],
'Gallery' => ['File'],
'Geo2Country' => ['Account', 'Admin', 'Classic', 'CountryCoordinates', 'FontAwesome', 'Login', 'News', 'Perf', 'Recovery', 'Register'],
'GTranslate' => [],
'Guestbook' => ['Admin'],
'Hash' => [],
'Helpdesk' => ['Comments'],
'HTML' => [],
'Hydra' => [],
'ImageMagick' => [],
'Instagram' => [],
'Install' => [],
'Invite' => [],
'IP2Country' => ['Country'],
'ITMB' => ['Account', 'ActivationAlert', 'Address', 'Admin', 'Avatar', 'Contact', 'FontAtkinson', 'Markdown', 'Mibbit', 'News', 'Perf', 'PM', 'Register', 'Recovery'],
'Javascript' => [],
'JPGraph' => [],
'JQuery' => [],
'JQueryAutocomplete' => ['JQuery'],
'KassiererCard' => ['Account', 'AboutMe', 'ActivationAlert', 'Address', 'Admin', 'Ads', 'Avatar', 'Backup', 'Birthday', 'Bootstrap5', 'Bootstrap5Theme', 'Captcha', 'Category', 'CKEditor', 'Contact', 'CountryCoordinates', 'CountryRestrictions', 'Cronjob', 'CSS', 'DoubleAccounts', 'FontAtkinson', 'FontAwesome', 'Forum', 'GTranslate', 'IP2Country', 'Javascript', 'JQueryAutocomplete', 'Licenses', 'Links', 'LoC', 'Login', 'Maps', 'Mail', 'Maps', 'News', 'PaymentBank', 'PaymentCredits', 'PaymentPaypal', 'Perf', 'Poll', 'PM', 'QRCode', 'Recovery', 'Register', 'Sitemap', 'TorDetection', 'VPNDetect', 'YouTube'],
'Language' => [],
'Licenses' => [],
'Links' => ['Votes', 'Tags', 'Cronjob'],
'LinkUUp' => ['Account', 'ActivationAlert', 'Address', 'Admin', 'Avatar', 'Backup', 'Birthday', 'Captcha', 'Classic', 'Comments', 'Contact', 'CORS', 'Country', 'CSS', 'Currency', 'DSGVO', 'Facebook', 'Friends', 'Gallery', 'Instagram', 'Javascript', 'JPGraph', 'JQueryAutocomplete', 'Licenses', 'Login', 'Maps', 'Markdown', 'News', 'OpenTimes', 'Perf', 'QRCode', 'Recovery', 'Register', 'Websocket'],
'LoC' => [],
'Login' => ['Session'],
'Mail' => ['Mailer', 'Net'],
'Mailer' => ['Mail'],
'Maintenance' => [],
'Maps' => ['JQuery'],
'Markdown' => ['HTML', 'JQuery', 'FontAwesome'],
'Math' => [],
'Mettwitze' => ['Account', 'Admin', 'Bootstrap5Theme', 'Classic', 'Comments', 'JQueryAutocomplete', 'Login', 'Register', 'Recovery', 'Sitemap', 'Votes'],
'Mibbit' => [],
'Moment' => [],
'Net' => [],
'News' => ['Comments', 'Category', 'Mail'],
'OnlineUsers' => [],
'OpenTimes' => [],
'Payment' => ['Address', 'TCPDF', 'Mail'],
'PaymentBank' => ['Payment'],
'PaymentCredits' => ['Payment'],
'PaymentPaypal' => ['Payment'],
'PaypalDonations' => [],
'Perf' => [],
'PM' => ['Account'],
'PMA' => [],
'Poll' => [],
'Prism' => [],
'Python' => [],
'QRCode' => [],
'Quotes' => ['Address', 'Votes'],
'Recalcolo' => ['Login', 'Register', 'Account', 'Forum', 'Contact', 'Admin', 'News', 'PaymentBank', 'PaymentCredits', 'PaymentPaypal'],
'Recovery' => ['Mail'],
'Register' => [],
'Security' => ['Hash'],
'Session' => [],
'Shoutbox' => [],
'SimpleMDE' => ['HTML'],
'Sitemap' => [],
'Statistics' => [],
'Table' => [],
'Tags' => [],
'TBS' => ['Admin', 'Avatar', 'Captcha', 'Classic', 'Country', 'Contact', 'CSS', 'Favicon', 'FontAwesome', 'Forum', 'Javascript', 'JQueryAutocomplete', 'Login', 'Markdown', 'Mibbit', 'News', 'OnlineUsers', 'Perf', 'PM', 'Python', 'Recovery', 'Register', 'Statistics'],
'TCPDF' => [],
'Tests' => [],
'Todo' => [],
'TorDetection' => ['Net'],
'Tradestation' => ['Account', 'Admin', 'CLI'],
'UI' => [],
'User' => ['Core'],
'Votes' => [],
'VPNDetect' => [],
'Websocket' => ['Session'],
'YouTube' => ['File'],
'YTBest' => ['Admin', 'Classic', 'Comments', 'Login', 'Recovery', 'Register', 'Votes', 'YouTube'],
'ZIP' => [],
### END_DEPENDENCIES ###
	];

}
    