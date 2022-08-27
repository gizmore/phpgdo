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
	 * Provider packages.
	 * Multi-Provided is first
	 */
	public static array $PROVIDERS = [
		'Captcha' => ['phpgdo-captcha', 'phpgdo-recaptcha2'],
		'DBMS' => ['phpgdo-mysql', 'phpgdo-postgres', 'phpgdo-sqlite'],
		'Mailer' => ['phpgdo-mailer-gdo', 'phpgdo-mailer-symfony'],
		'Session' => ['phpgdo-session-db', 'phpgdo-session-cookie'],
		'Account' => 'phpgdo-account',
		'ActivationAlert' => 'phpgdo-activation-alert',
		'Address' => 'phpgdo-address',
		'Aprilfools' => 'phpgdo-aprilfools',
		'Avatar' => 'phpgdo-avatar',
		'Backup' => 'phpgdo-backup',
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
		'Download' => 'phpgdo-download',
		'DSGVO' => 'phpgdo-dsgvo',
		'Facebook' => 'phpgdo-facebook',
		'Favicon' => 'phpgdo-favicon',
		'File' => 'phpgdo-file',
		'Fineprint' => 'phpgdo-fineprint',
		'Follower' => 'phpgdo-follower',
		'FontAwesome' => 'phpgdo-font-awesome',
		'Forum' => 'phpgdo-forum',
		'Friends' => 'phpgdo-friends',
		'Gallery' => 'phpgdo-gallery',
		'Geo2Country' => 'phpgdo-geo2country',
		'Guestbook' => 'phpgdo-guestbook',
		'Hash' => 'phpgdo-hash',
		'Helpdesk' => 'phpgdo-helpdesk',
		'Instagram' => 'phpgdo-instagram',
		'Invite' => 'phpgdo-invite',
		'IP2Country' => 'phpgdo-ip2country',
		'Javascript' => 'phpgdo-javascript',
		'JPGraph' => 'phpgdo-jpgraph',
		'JQuery' => 'phpgdo-jquery',
		'JQueryAutocomplete' => 'phpgdo-jquery-autocomplete',
		'Licenses' => 'phpgdo-licenses',
		'Links' => 'phpgdo-links',
		'LinkUUp' => '',
		'Login' => 'phpgdo-login',
		'Mail' => 'phpgdo-mail',
		'Mailer' => 'phpgdo-mailer',
		'Maps' => 'phpgdo-maps',
		'Markdown' => 'phpgdo-markdown',
		'Math' => 'phpgdo-math',
		'Moment' => 'phpgdo-moment',
		'News' => 'phpgdo-news',
		'OnlineUsers' => 'phpgdo-online-users',
		'OpenTimes' => 'phpgdo-open-times',
		'Payment' => 'phpgdo-payment',
		'PaymentBank' => 'phpgdo-payment-bank',
		'PaymentCredits' => 'phpgdo-payment-credits',
		'PaymentPaypal' => 'phpgdo-payment-paypal',
		'PM' => 'phpgdo-pm',
		'Poll' => 'phpgdo-poll',
		'Prism' => 'phpgdo-prism',
		'QRCode' => 'phpgdo-qrcode',
		'Quotes' => 'phpgdo-quotes',
		'Recovery' => 'phpgdo-recovery',
		'Register' => 'phpgdo-register',
		'Security' => 'phpgdo-security',
		'Shoutbox' => 'phpgdo-shoutbox',
		'SimpleMDE' => 'phpgdo-simple-mde',
		'Sitemap' => 'phpgdo-sitemap',
		'Tags' => 'phpgdo-tags',
		'Todo' => 'phpgdo-todo',
		'Votes' => 'phpgdo-votes',
		'Websocket' => 'phpgdo-websocket',
		'ZIP' => 'phpgdo-zip',
	];

	public static $DEPENDENCIES = [
		
		'Account' => ['Login'],
		'ActivationAlert' => [],
		'Address' => [],
		'Admin' => ['Table'],
		'Aprilfools' => [],
		'Avatar' => ['File'],
		'Backup' => ['ZIP', 'Cronjob'],
		'BasicAuth' => [],
		'Birthday' => [],
		'Bootstrap5' => ['Core', 'JQuery'],
		'Bootstrap5Theme' => ['Bootstrap5'],
		'Captcha' => [],
		'Category' => [],
		'CKEditor' => ['JQuery'],
		'Classic' => [],
		'CLI' => [],
		'Comments' => ['Votes', 'File'],
		'Contact' => [],
		'Core' => ['Language', 'Date', 'UI', 'User'],
		'CORS' => [],
		'Country' => [],
		'CountryCoordinates' => ['Country', 'Maps'],
		'Cronjob' => [],
		'Crypto' => [],
		'CSS' => [],
		'Currency' => ['Cronjob'],
		'Date' => [],
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
		'Download' => ['Payment'],
		'DSGVO' => [],
		'Facebook' => [],
		'Favicon' => [],
		'File' => [],
		'Fineprint' => ['Classic', 'DOMPDF', 'JQueryAutocomplete'],
		'Follower' => [],
		'FontAwesome' => [],
		'Forum' => ['File'],
		'Friends' => [],
		'Gallery' => ['File'],
		'GDO7Landing' => [],
		'Geo2Country' => ['Account', 'Admin', 'Classic', 'CountryCoordinates', 'FontAwesome', 'Login', 'News', 'Perf', 'Recovery', 'Register'],
		'Git' => [],
		'Gitwatch' => ['Git', 'Bootstrap5Theme', 'Session'],
		'Guestbook' => ['Admin'],
		'Hash' => [],
		'Helpdesk' => ['Comments'],
		'Instagram' => [],
		'Install' => [],
		'Invite' => [],
		'IP2Country' => ['Country'],
		'Javascript' => [],
		'JPGraph' => [],
		'JQuery' => [],
		'JQueryAutocomplete' => ['JQuery'],
		'Language' => [],
		'Licenses' => [],
		'Links' => ['Votes', 'Tags', 'Cronjob'],
		'LinkUUp' => ['Account', 'Websocket', 'Comments', 'Login', 'Recovery', 'Register', 'Avatar', 'Gallery', 'Admin', 'Contact', 'Facebook', 'Instagram', 'OpenTimes', 'Friends', 'Address', 'Maps', 'QRCode', 'Bootstrap5Theme', 'CORS', 'JPGraph', 'Markdown', 'News', 'ActivationAlert', 'Perf', 'Birthday', 'Backup'],
		'Login' => ['Session'],
		'Mail' => ['Mailer'],
		'Mailer' => [],
		'Maps' => [],
		'Markdown' => ['JQuery', 'FontAwesome'],
		'Math' => [],
		'Moment' => [],
		'Net' => [],
		'News' => ['Comments', 'Category', 'Mail'],
		'OnlineUsers' => [],
		'OpenTimes' => [],
		'Payment' => ['Address', 'TCPDF'],
		'PaymentBank' => ['Payment'],
		'PaymentCredits' => ['Payment'],
		'PaymentPaypal' => ['Payment'],
		'Perf' => [],
		'PHPInfo' => [],
		'PM' => ['Account'],
		'Poll' => [],
		'Prism' => [],
		'QRCode' => [],
		'Quotes' => ['Votes', 'Realname'],
		'Realname' => [],
		'Recovery' => ['Captcha'],
		'Register' => [],
		'Security' => ['Hash'],
		'Session' => [],
		'Shoutbox' => [],
		'SimpleMDE' => ['Core'],
		'Sitemap' => [],
		'Table' => [],
		'Tests' => [],
		'Todo' => [],
		'UI' => [],
		'User' => ['Core'],
		'Votes' => [],
		'Websocket' => [],
		'ZIP' => [],
	];

}
    