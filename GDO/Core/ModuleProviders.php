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
'AboutMe' => ['Account'],
'Account' => ['Login'],
'ACME' => ['Net'],
'ActivationAlert' => [],
'Address' => ['Mail', 'Country'],
'Admin' => ['Table'],
'Ads' => ['Payment'],
'Aprilfools' => [],
'Avatar' => ['File'],
'Backup' => ['ZIP', 'Cronjob'],
'BasicAuth' => [],
'Birthday' => [],
'Bootstrap5' => ['JQuery'],
'Bootstrap5Theme' => ['Bootstrap5', 'Moment'],
'Captcha' => ['Session'],
'Category' => [],
'ChatGPT' => ['File'],
'CKEditor' => ['HTML', 'JQuery'],
'Classic' => [],
'CLI' => [],
'Codinggeex' => ['Admin', 'Bootstrap5Theme', 'Download', 'Login', 'Perf'],
'Comments' => ['Votes', 'File'],
'Contact' => ['Mail'],
'Core' => ['Crypto', 'Date', 'DBMS', 'Form', 'Language', 'UI', 'User'],
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
'Diary' => ['Admin', 'Birthday', 'Classic', 'Contact', 'Login', 'News'],
'Dog' => ['CLI', 'Cronjob', 'Net'],
'DogAuth' => ['Dog', 'Login', 'Register'],
'DogBlackjack' => ['Dog'],
'DogChatGPT' => ['ChatGPT', 'Dog'],
'DogGreetings' => ['Dog'],
'DogIRC' => ['DogAuth'],
'DogIRCAutologin' => ['DogAuth', 'DogIRC'],
'DogIRCSpider' => ['DogIRC'],
'DogShadowdogs' => ['DogAuth'],
'DogTeams' => ['Dog'],
'DogTick' => ['Country', 'DogIRC'],
'DogWebsite' => ['Admin', 'Avatar', 'Bootstrap5Theme', 'Contact', 'Dog', 'DogAuth', 'DogBlackjack', 'DogGreetings', 'DogIRC', 'DogIRCAutologin', 'DogIRCSpider', 'DogShadowdogs', 'DogTick', 'Download', 'Forum', 'JQuery', 'Links', 'Markdown', 'News', 'Perf', 'PM', 'Quotes', 'Shoutbox', 'Todo'],
'DOMPDF' => ['File'],
'DoubleAccounts' => [],
'Download' => ['Category', 'File', 'Payment', 'Votes'],
'DSGVO' => ['Session'],
'EdwardSnowdenLand' => ['AboutMe', 'Account', 'ActivationAlert', 'Admin', 'Avatar', 'Bootstrap5Theme', 'Cronjob', 'IP2Country', 'Favicon', 'File', 'FontAwesome', 'Invite', 'JQueryAutocomplete', 'Mail', 'News', 'Register', 'Votes'],
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
'Geo2City' => ['Geo2Country', 'ZIP'],
'Geo2Country' => ['Account', 'Admin', 'Classic', 'CountryCoordinates', 'CSS', 'FontAwesome', 'Javascript', 'Login', 'News', 'Perf', 'Recovery', 'Register'],
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
'ITMB' => ['Account', 'ActivationAlert', 'Address', 'Admin', 'Avatar', 'Bootstrap5Theme', 'Contact', 'FontAtkinson', 'Markdown', 'Mibbit', 'News', 'Perf', 'PM', 'Register', 'Recovery'],
'Javascript' => [],
'JPGraph' => [],
'JQuery' => [],
'JQueryAutocomplete' => ['JQuery'],
'KassiererCard' => ['Account', 'AboutMe', 'ActivationAlert', 'Address', 'Admin', 'Ads', 'Avatar', 'Backup', 'Birthday', 'Bootstrap5', 'Bootstrap5Theme', 'Captcha', 'Category', 'CKEditor', 'Contact', 'CountryCoordinates', 'CountryRestrictions', 'Cronjob', 'CSS', 'DoubleAccounts', 'FontAtkinson', 'FontAwesome', 'Forum', 'GTranslate', 'IP2Country', 'Javascript', 'JQueryAutocomplete', 'Licenses', 'Links', 'LoC', 'Login', 'Maps', 'Mail', 'Maps', 'News', 'PaymentBank', 'PaymentCredits', 'PaymentPaypal', 'Perf', 'Poll', 'PM', 'QRCode', 'Recovery', 'Register', 'Sitemap', 'TorDetection', 'VPNDetect', 'YouTube'],
'Language' => [],
'Licenses' => [],
'Links' => ['Votes', 'Tags', 'Cronjob'],
'LinkUUp' => ['AboutMe', 'Account', 'ActivationAlert', 'Address', 'Admin', 'Avatar', 'Backup', 'Birthday', 'Bootstrap5Theme', 'Captcha', 'Classic', 'Comments', 'Contact', 'CORS', 'Country', 'CSS', 'Currency', 'DSGVO', 'Facebook', 'Friends', 'Gallery', 'Instagram', 'Javascript', 'JPGraph', 'JQueryAutocomplete', 'Licenses', 'Login', 'Maps', 'Markdown', 'News', 'OpenTimes', 'Perf', 'QRCode', 'Recovery', 'Register', 'Websocket'],
'LoC' => [],
'Login' => ['Session'],
'Mail' => ['Mailer', 'Net'],
'Mailer' => ['Mail'],
'Maintenance' => [],
'Maps' => ['JQuery'],
'Markdown' => ['HTML', 'JQuery', 'FontAwesome'],
'Math' => [],
'Mettwitze' => ['Account', 'Admin', 'Bootstrap5Theme', 'Classic', 'Comments', 'JQueryAutocomplete', 'Login', 'Recovery', 'Register', 'Sitemap', 'Votes'],
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
'PHPGDO' => [],
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
'Shadowlamb' => [],
'Shoutbox' => [],
'SimpleMDE' => ['HTML'],
'Sitemap' => [],
'Statistics' => [],
'Table' => [],
'Tags' => [],
'TBS' => ['Admin', 'Avatar', 'Captcha', 'Classic', 'Country', 'Contact', 'Cronjob', 'CSS', 'Favicon', 'FontAwesome', 'Forum', 'Javascript', 'JQueryAutocomplete', 'Login', 'Markdown', 'Mibbit', 'News', 'OnlineUsers', 'Perf', 'PM', 'Python', 'Recovery', 'Register', 'Statistics'],
'TCPDF' => [],
'Tests' => [],
'Todo' => ['Table'],
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
