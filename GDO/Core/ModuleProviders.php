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
'Mailer' => ['phpgdo-mailer', 'phpgdo-phpmailer', 'phpgdo-mailer-symfony'],
'Session' => ['phpgdo-session-db', 'phpgdo-session-cookie'],
'AboutMe' => 'phpgdo-about-me',
'Account' => 'phpgdo-account',
'ACME' => 'phpgdo-acme',
'ActivationAlert' => 'phpgdo-activation-alert',
'Address' => 'phpgdo-address',
'Ads' => 'phpgdo-ads',
'ApiHub' => 'phpgdo-apihub',
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
'DogFriends' => 'phpgdo-dog-friends',
'DogGreetings' => 'gdo6-dog-greetings',
'DogIRC' => 'gdo6-dog-irc',
'DogIRCAutologin' => 'gdo6-dog-irc-autologin',
'DogIRCSpider' => 'gdo6-dog-irc-spider',
'DogMail' => 'phpgdo-dog-mail',
'DogMastodon' => 'phpgdo-dog-mastodon',
'DogNinja' => 'phpgdo-dog-ninja',
'DogOracle' => 'phpgdo-dog-oracle',
'DogRSS' => 'phpgdo-dog-rss',
'DogShadowdogs' => 'gdo6-dog-shadowdogs',
'DogSlapwarz' => 'phpgdo-dog-slapwarz',
'DogTeams' => 'phpgdo-dog-teams',
'DogTelegram' => 'phpgdo-dog-telegram',
'DogTick' => 'gdo6-dog-tick',
'DogTwitter' => 'phpgdo-dog-twitter',
'DogWeather' => 'phpgdo-dog-weather',
'DogWebsite' => 'gdo6-dog-website',
'DogWhatsApp' => 'phpgdo-dog-whatsapp',
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
'HOL' => 'phpgdo-hue-of-life',
'HTML' => 'phpgdo-html',
'Hydra' => 'phpgdo-hydra',
'ImageMagick' => 'phpgdo-image-magick',
'Instagram' => 'phpgdo-instagram',
'Invite' => 'phpgdo-invite',
'IP2City' => 'phpgdo-ip2city',
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
'RegisterID' => 'phpgdo-register-id',
'Security' => 'phpgdo-security',
'Shadowlamb' => 'phpgdo-shadowlamb',
'Shoutbox' => 'phpgdo-shoutbox',
'SimpleMDE' => 'phpgdo-simple-mde',
'Sitemap' => 'phpgdo-sitemap',
'Statistics' => 'phpgdo-statistics',
'Subscription' => 'phpgdo-subscription',
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
'AboutMe' => ['Account'],
'Account' => ['Login'],
'ACME' => ['Net'],
'ActivationAlert' => [],
'Address' => ['Mail', 'Maps', 'Country'],
'Admin' => ['Table'],
'Ads' => ['Payment'],
'ApiHub' => ['Net'],
'Aprilfools' => [],
'Avatar' => ['File'],
'Backup' => ['Cronjob', 'ZIP'],
'BasicAuth' => [],
'Birthday' => [],
'Bootstrap5' => ['JQuery'],
'Bootstrap5Theme' => ['Bootstrap5', 'Moment'],
'Captcha' => ['Session'],
'Category' => [],
'ChatGPT' => ['Dog'],
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
'DogFriends' => ['Dog'],
'DogGreetings' => ['Dog'],
'DogIRC' => ['DogAuth'],
'DogIRCAutologin' => ['DogAuth', 'DogIRC'],
'DogIRCSpider' => ['DogIRC'],
'DogMail' => ['Dog'],
'DogNinja' => ['Dog', 'Net'],
'DogOracle' => ['DogAuth', 'Poll', 'Subscription'],
'DogRSS' => ['Dog', 'Net', 'News'],
'DogShadowdogs' => ['DogAuth'],
'DogSlapwarz' => ['Dog'],
'DogTeams' => ['Dog'],
'DogTelegram' => ['Dog', 'Subscription'],
'DogTick' => ['Country', 'DogIRC'],
'DogTwitter' => [],
'DogWeather' => ['Dog'],
'DogWebsite' => ['Admin', 'Avatar', 'Bootstrap5Theme', 'Captcha', 'ChatGPT', 'Contact', 'Dog', 'DogAuth', 'DogBlackjack', 'DogFriends', 'DogGreetings', 'DogIRC', 'DogIRCAutologin', 'DogIRCSpider', 'DogOracle', 'DogRSS', 'DogShadowdogs', 'DogSlapwarz', 'DogTelegram', 'DogTick', 'DogWeather', 'Download', 'Forum', 'JQuery', 'Links', 'Markdown', 'Moment', 'News', 'Perf', 'PM', 'Quotes', 'Shoutbox', 'Todo'],
'DogWhatsApp' => ['Dog'],
'DOMPDF' => ['File'],
'DoubleAccounts' => [],
'Download' => ['Category', 'File', 'Payment', 'Votes'],
'DSGVO' => ['Session'],
'EdwardSnowdenLand' => ['AboutMe', 'Account', 'ActivationAlert', 'Admin', 'Avatar', 'Backup', 'Bootstrap5Theme', 'Captcha', 'Contact', 'Cronjob', 'IP2Country', 'Favicon', 'File', 'FontAwesome', 'Forum', 'Invite', 'Javascript', 'JQueryAutocomplete', 'Mail', 'News', 'PM', 'Recovery', 'Register', 'TorChallenge', 'Votes'],
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
'GDO6DB' => [],
'Geo2City' => ['Geo2Country', 'ZIP'],
'Geo2Country' => ['Account', 'Admin', 'Classic', 'CountryCoordinates', 'CSS', 'FontAwesome', 'Javascript', 'Login', 'News', 'Perf', 'Recovery', 'Register'],
'GTranslate' => [],
'Guestbook' => ['Admin'],
'Hash' => [],
'Helpdesk' => ['Comments'],
'HOL' => ['Admin', 'Bootstrap5Theme', 'JQuery', 'Login', 'Register', 'Session'],
'HTML' => [],
'Hydra' => [],
'ImageMagick' => [],
'Instagram' => [],
'Install' => [],
'Invite' => [],
'IP2City' => ['Address', 'Country', 'Cronjob'],
'IP2Country' => ['Country'],
'ITMB' => ['Account', 'ActivationAlert', 'Address', 'Admin', 'Avatar', 'Bootstrap5Theme', 'Contact', 'FontAtkinson', 'Markdown', 'Mibbit', 'News', 'Perf', 'PM', 'Recovery', 'TCPDF'],
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
'Mettwitze' => ['Account', 'Admin', 'Bootstrap5Theme', 'Classic', 'Comments', 'GDO6DB', 'JQueryAutocomplete', 'Login', 'Recovery', 'Register', 'Sitemap', 'Votes'],
'Mibbit' => [],
'Moment' => [],
'Net' => [],
'News' => ['Comments', 'Category', 'Mail'],
'OnlineUsers' => [],
'OpenTimes' => [],
'Payment' => ['Account', 'Address', 'TCPDF', 'Mail'],
'PaymentBank' => ['Payment'],
'PaymentCredits' => ['Payment'],
'PaymentPaypal' => ['Payment'],
'PaypalDonations' => [],
'PDFParser' => [],
'Perf' => [],
'PHPGDO' => [],
'PM' => ['Account'],
'PMA' => [],
'Poll' => ['Subscription'],
'Prism' => [],
'Python' => [],
'QRCode' => [],
'Quotes' => ['Address', 'Votes'],
'Recalcolo' => ['Account', 'Admin', 'Bootstrap5Theme', 'CLI', 'Contact', 'Forum', 'IP2City', 'JQueryAutocomplete', 'Login', 'News', 'PaymentBank', 'PaymentCredits', 'PaymentPaypal', 'Register'],
'Recovery' => ['Mail'],
'Register' => [],
'RegisterID' => [],
'Security' => ['Hash'],
'Session' => [],
'Shadowlamb' => [],
'Shoutbox' => [],
'SimpleMDE' => ['HTML'],
'Sitemap' => [],
'Statistics' => [],
'Subscription' => [],
'Table' => [],
'Tags' => [],
'TBS' => ['Admin', 'Avatar', 'Captcha', 'Classic', 'Country', 'Contact', 'Cronjob', 'CSS', 'Favicon', 'FontAwesome', 'Forum', 'Javascript', 'JQueryAutocomplete', 'Login', 'Markdown', 'Mibbit', 'News', 'OnlineUsers', 'Perf', 'PM', 'Python', 'Recovery', 'Register', 'Statistics'],
'TCPDF' => [],
'TesseractOCR' => ['ImageMagick'],
'Tests' => [],
'Todo' => ['Table'],
'TorChallenge' => ['TorDetection'],
'TorDetection' => ['Net'],
'Tradestation' => ['Account', 'Admin', 'CLI', 'PDFParser', 'TesseractOCR'],
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
