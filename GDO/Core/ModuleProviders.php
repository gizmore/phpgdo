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
'DogMastodon' => 'phpgdo-dog-mastodon',
'DogNinja' => 'phpgdo-dog-ninja',
'DogOracle' => 'phpgdo-dog-oracle',
'DogShadowdogs' => 'gdo6-dog-shadowdogs',
'DogTeams' => 'phpgdo-dog-teams',
'DogTelegram' => 'phpgdo-dog-telegram',
'DogTick' => 'gdo6-dog-tick',
'DogTwitter' => 'phpgdo-dog-twitter',
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

Warning: Undefined array key "REMOTE_ADDR" in C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5165    4698272   3. GDO\Core\Logger::logException($e = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:439
    0.5165    4698592   4. GDO\Core\Logger::log($filename = 'critical', $message = 'syntax error, unexpected token "}", expecting variable in C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php Line 44\n', $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:317
    0.5175    4774032   5. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Undefined array key "REMOTE_ADDR"', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php', $errline = 181, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181
    0.5176    4775024   6. GDO\Core\Logger::logCritical($message = 'Undefined array key "REMOTE_ADDR" in C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181\nBacktrace starts in [unknown file] line ?.\n - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 317.\n - GDO\Core\Logger::log(&quot;critical&quot;, &quot;syntax error, unexpected token \&quot;}\&quot;, expecting variable in C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Mo…php '...) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:193
    0.5176    4775024   7. GDO\Core\Logger::log($filename = 'critical', $message = 'Undefined array key "REMOTE_ADDR" in C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181\nBacktrace starts in [unknown file] line ?.\n - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 317.\n - GDO\Core\Logger::log(&quot;critical&quot;, &quot;syntax error, unexpected token \&quot;}\&quot;, expecting variable in C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Mo…php '..., $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:311


Warning: Cannot modify header information - headers already sent by (output started at C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in C:\ProjektPHPGDO\phpgdo\GDO7.php on line 345

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5165    4698272   3. GDO\Core\Logger::logException($e = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:439
    0.5165    4698592   4. GDO\Core\Logger::log($filename = 'critical', $message = 'syntax error, unexpected token "}", expecting variable in C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php Line 44\n', $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:317
    0.5175    4774032   5. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Undefined array key "REMOTE_ADDR"', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php', $errline = 181, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181
    0.5420    4775968   6. hdrc($header = 'HTTP/1.1 500 Server Error', $replace = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:209
    0.5420    4775968   7. hdr($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:324
    0.5420    4775968   8. header($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:345

<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Undefined array key "REMOTE_ADDR"&nbsp;in&nbsp;<b style=/"font-size:16px;/">C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">181</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 317.
 - GDO\Core\Logger::log(&quot;critical&quot;, &quot;syntax error, unexpected token \&quot;}\&quot;, expecting variable in C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Mo…php Line 44\n&quot;, 8)  C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181.</pre>

Warning: Undefined array key "REMOTE_ADDR" in C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5165    4698272   3. GDO\Core\Logger::logException($e = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:439
    0.5538    4779528   4. GDO\Core\Logger::log($filename = 'critical', $message = '<span style="color: red;">ParseError</span>: ´<i>syntax error, unexpected token "}", expecting variable</i>´ in <b>C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php</b> line <b>44</b>\nBacktrace starts in C:/ProjektPHPGDO/phpgdo/provider_dependencies.php line 26.\n - GDO\Core\ModuleLoader-&gt;loadModules(false, true, true) ................................................................. C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 277.\n - GDO\Core\ModuleLoader-&gt;loadModulesFS(false) ....'..., $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:319
    0.5538    4779592   5. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Undefined array key "REMOTE_ADDR"', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php', $errline = 181, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181
    0.5543    4780552   6. GDO\Core\Logger::logCritical($message = 'Undefined array key "REMOTE_ADDR" in C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181\nBacktrace starts in [unknown file] line ?.\n - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 319.\n - GDO\Core\Logger::log(&quot;critical&quot;, &quot;&amp;lt;span style=\&quot;color: red;\&quot;&amp;gt;ParseError&amp;lt;/span&amp;gt;: \u00b4&amp;lt;i&amp;gt;syntax e'...) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:193
    0.5543    4780552   7. GDO\Core\Logger::log($filename = 'critical', $message = 'Undefined array key "REMOTE_ADDR" in C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181\nBacktrace starts in [unknown file] line ?.\n - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 319.\n - GDO\Core\Logger::log(&quot;critical&quot;, &quot;&amp;lt;span style=\&quot;color: red;\&quot;&amp;gt;ParseError&amp;lt;/span&amp;gt;: \u00b4&amp;lt;i&amp;gt;syntax e'..., $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:311


Warning: Cannot modify header information - headers already sent by (output started at C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in C:\ProjektPHPGDO\phpgdo\GDO7.php on line 345

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5165    4698272   3. GDO\Core\Logger::logException($e = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:439
    0.5538    4779528   4. GDO\Core\Logger::log($filename = 'critical', $message = '<span style="color: red;">ParseError</span>: ´<i>syntax error, unexpected token "}", expecting variable</i>´ in <b>C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php</b> line <b>44</b>\nBacktrace starts in C:/ProjektPHPGDO/phpgdo/provider_dependencies.php line 26.\n - GDO\Core\ModuleLoader-&gt;loadModules(false, true, true) ................................................................. C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 277.\n - GDO\Core\ModuleLoader-&gt;loadModulesFS(false) ....'..., $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:319
    0.5538    4779592   5. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Undefined array key "REMOTE_ADDR"', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php', $errline = 181, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181
    0.5701    4780360   6. hdrc($header = 'HTTP/1.1 500 Server Error', $replace = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:209
    0.5701    4780360   7. hdr($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:324
    0.5701    4780360   8. header($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:345

<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Undefined array key "REMOTE_ADDR"&nbsp;in&nbsp;<b style=/"font-size:16px;/">C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">181</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - GDO\Core\Logger::logException(ParseError) ............................................................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 319.
 - GDO\Core\Logger::log(&quot;critical&quot;, &quot;&amp;lt;span style=\&quot;color: red;\&quot;&amp;gt;ParseError&amp;lt;/span&amp;gt;: \u00b4&amp;lt;i&amp;gt;syntax error, unexpected … line 44.\r\n&quot;, 8)  C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php line 181.</pre>

Warning: Undefined array key "REMOTE_ADDR" in C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php on line 181

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5868    4777064   3. hdrc($header = 'HTTP/1.1 500 Server Error', $replace = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:452
    0.5868    4777064   4. hdr($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:324
    0.5868    4777064   5. header($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:345
    0.5868    4777288   6. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Cannot modify header information - headers already sent by (output started at C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181)', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO7.php', $errline = 345, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO7.php:345
    0.5870    4778248   7. GDO\Core\Logger::logCritical($message = 'Cannot modify header information - headers already sent by (output started at C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php:181) in C:/ProjektPHPGDO/phpgdo/GDO7.php line 345\nBacktrace starts in [unknown file] line ?.\n - hdrc(&quot;HTTP/1.1 500 Server Error&quot;) ........ C:/ProjektPHPGDO/phpgdo/GDO7.php line 324.\n - hdr(&quot;HTTP/1.1 500 Server Error&quot;, true) ... C:/ProjektPHPGDO/phpgdo/GDO7.php line 345.\n - header(&quot;HTTP/1.1 500 Server Error&quot;, true)  [unknown file] line ?.') C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:193
    0.5870    4778248   8. GDO\Core\Logger::log($filename = 'critical', $message = 'Cannot modify header information - headers already sent by (output started at C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php:181) in C:/ProjektPHPGDO/phpgdo/GDO7.php line 345\nBacktrace starts in [unknown file] line ?.\n - hdrc(&quot;HTTP/1.1 500 Server Error&quot;) ........ C:/ProjektPHPGDO/phpgdo/GDO7.php line 324.\n - hdr(&quot;HTTP/1.1 500 Server Error&quot;, true) ... C:/ProjektPHPGDO/phpgdo/GDO7.php line 345.\n - header(&quot;HTTP/1.1 500 Server Error&quot;, true)  [unknown file] line ?.', $logmode = 8) C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:311


Warning: Cannot modify header information - headers already sent by (output started at C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181) in C:\ProjektPHPGDO\phpgdo\GDO7.php on line 345

Call Stack:
    0.4942    4657688   1. GDO\Core\Debug::exception_handler($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:0
    0.4942    4657688   2. GDO\Core\Debug::debugException($ex = class ParseError { protected $message = 'syntax error, unexpected token "}", expecting variable'; private string ${Error}string = ''; protected $code = 0; protected string $file = 'C:\ProjektPHPGDO\phpgdo\GDO\DogNinja\Module_DogNinja.php'; protected int $line = 44; private array ${Error}trace = [0 => [...], 1 => [...], 2 => [...], 3 => [...], 4 => [...], 5 => [...], 6 => [...], 7 => [...], 8 => [...]]; private ?Throwable ${Error}previous = NULL }, $render = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:421
    0.5868    4777064   3. hdrc($header = 'HTTP/1.1 500 Server Error', $replace = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:452
    0.5868    4777064   4. hdr($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:324
    0.5868    4777064   5. header($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:345
    0.5868    4777288   6. GDO\Core\Debug::error_handler($errno = 2, $errstr = 'Cannot modify header information - headers already sent by (output started at C:\ProjektPHPGDO\phpgdo\GDO\Core\Logger.php:181)', $errfile = 'C:\ProjektPHPGDO\phpgdo\GDO7.php', $errline = 345, $errcontext = ???) C:\ProjektPHPGDO\phpgdo\GDO7.php:345
    0.6017    4778536   7. hdrc($header = 'HTTP/1.1 500 Server Error', $replace = ???) C:\ProjektPHPGDO\phpgdo\GDO\Core\Debug.php:209
    0.6017    4778536   8. hdr($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:324
    0.6017    4778536   9. header($header = 'HTTP/1.1 500 Server Error', $replace = TRUE) C:\ProjektPHPGDO\phpgdo\GDO7.php:345

<div class="gdo-exception">
<em><p>PHP Warning(EH 2):&nbsp;Cannot modify header information - headers already sent by (output started at C:/ProjektPHPGDO/phpgdo/GDO/Core/Logger.php:181)&nbsp;in&nbsp;<b style=/"font-size:16px;/">C:/ProjektPHPGDO/phpgdo/GDO7.php</b>&nbsp;line&nbsp;<b style=/"font-size:16px;/">345</b></p></em><div class="gdt-hr"></div><pre>Backtrace starts in [unknown file] line ?.
 - hdrc(&quot;HTTP/1.1 500 Server Error&quot;) ........ C:/ProjektPHPGDO/phpgdo/GDO7.php line 324.
 - hdr(&quot;HTTP/1.1 500 Server Error&quot;, true) ... C:/ProjektPHPGDO/phpgdo/GDO7.php line 345.
 - header(&quot;HTTP/1.1 500 Server Error&quot;, true)  [unknown file] line ?.</pre>
<div class="gdo-exception">
<em><span style="color: red;">ParseError</span>: ´<i>syntax error, unexpected token "}", expecting variable</i>´ in <b>C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php</b> line <b>44</b></em><div class="gdt-hr"></div><pre>Backtrace starts in C:/ProjektPHPGDO/phpgdo/provider_dependencies.php line 26.
 - GDO\Core\ModuleLoader-&gt;loadModules(false, true, true) ................................................................. C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 277.
 - GDO\Core\ModuleLoader-&gt;loadModulesFS(false) ........................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 353.
 - GDO\Util\Filewalker::traverse(&quot;C:\ProjektPHPGDO\phpgdo/GDO/&quot;, NULL, NULL, [
    {},
    &quot;_loadModuleFS&quot;
], , false) ... C:/ProjektPHPGDO/phpgdo/GDO/Util/Filewalker.php line 24.
 - gizmore\Filewalker::traverse(&quot;C:\ProjektPHPGDO\phpgdo/GDO&quot;, NULL, NULL, [
    {},
    &quot;_loadModuleFS&quot;
], , false, &quot;\&quot;)  C:/ProjektPHPGDO/phpgdo/GDO/Util/php-filewalker/gizmore/Filewalker.php line 104.
 - call_user_func([
    {},
    &quot;_loadModuleFS&quot;
], &quot;DogNinja&quot;, &quot;C:\ProjektPHPGDO\phpgdo/GDO\DogNinja&quot;, false) ............ [unknown file] line ?.
 - GDO\Core\ModuleLoader-&gt;_loadModuleFS(&quot;DogNinja&quot;, &quot;C:\ProjektPHPGDO\phpgdo/GDO\DogNinja&quot;, false) ....................... C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 509.
 - GDO\Core\ModuleLoader-&gt;loadModuleFS(&quot;DogNinja&quot;) ....................................................................... C:/ProjektPHPGDO/phpgdo/GDO/Core/ModuleLoader.php line 187.
 - class_exists(&quot;GDO\DogNinja\Module_DogNinja&quot;) .......................................................................... [unknown file] line ?.
 - {closure}(&quot;GDO/DogNinja/Module_DogNinja&quot;) ............................................................................. C:/ProjektPHPGDO/phpgdo/GDO/DogNinja/Module_DogNinja.php line 44.</pre>
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
