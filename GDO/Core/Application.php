<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use function GDO\Perf\xdebug_get_function_count;
use GDO\Core\Method\Stub;
use GDO\DB\Database;

/**
 * Application runtime data.
 * Global error methods.
 * Rendering mode.
 * Global Application time and microtime.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 * @see GDT_Page
 */
class Application extends GDT
{
	use WithVerb;
	use WithInstance;
	
	public function __destruct()
	{
		parent::__destruct();
		if (class_exists('GDO\\Core\\Logger', false))
		{
			Logger::flush();
		}
	}
	
	################################
	### HREF COLLECTOR FOR TESTS ###
	################################
	public static array $HREFS = []; #PP#delete#

	################
	### App Time ###
	################
	public static int $TIME;
	public static float $MICROTIME;
// 	public static \DateTime $DATETIME;
	public static function time(float $time) : void
	{
		self::$TIME = (int)$time;
		self::$MICROTIME = $time;
// 		self::$DATETIME = new \DateTime(Time::getDate($time));
	}
	
	public static function updateTime() : void
	{
		self::time(microtime(true));
	}
	
	public static function getRuntime() : float
	{
		return microtime(true) - GDO_TIME_START;
	}
	
	/**
	 * Call this at least.
	 */
	public static function init()
	{
		global $me;
		$me = Stub::make();
		return $me ? self::instance() : null;
	}

	/**
	 * Perf headers as cheap as possible.
	 * Query count, memory usage, timing and call count.
	 */
	public function timingHeader()
	{
		$m1 = memory_get_peak_usage(true);
		$m2 = memory_get_peak_usage(false);
		hdr(sprintf('X-GDO-DB: %d', Database::$QUERIES));
		hdr(sprintf('X-GDO-MEM: %s', max([$m1, $m2])));
		hdr(sprintf('X-GDO-TIME: %.01fms', $this->getRuntime() * 1000.0));
		if (function_exists('xdebug_get_function_count'))
		{
			hdr(sprintf('X-GDO-FUNC: %d', \xdebug_get_function_count()));
		}
	}
	
	#################
	### HTTP Code ###
	#################
	/**
	 * HTTP Response Code.
	 */
	public static int $RESPONSE_CODE = 200;
	
	/**
	 * Set the HTTP response code.
	 */
	public static function setResponseCode(int $code): void
	{
		if ($code > self::$RESPONSE_CODE)
		{
// 			if (defined('GDO_CORE_STABLE'))
// 			{
// 				xdebug_break();
// 			}
			self::$RESPONSE_CODE = $code;
		}
	}
	
	public static function isCrash() : bool
	{
		return self::$RESPONSE_CODE >= 500;
	}

	public static function isError() : bool
	{
		return self::$RESPONSE_CODE >= 400;
	}
	
	public static function isSuccess() : bool
	{
		return self::$RESPONSE_CODE < 400;
	}
	
	#########################
	### Application state ###
	#########################
	public function isTLS() : bool { return (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off'); }
	public function isAPI() : bool { return $this->isAjax() || $this->isJSON() || $this->isXML(); }
	public function isWebserver() : bool { return !$this->isCLI(); }
	# Render
	public function isCLI() : bool { return $this->cli; }
	public function isAjax() : bool { return $this->ajax; }
	public function isWebsocket() : bool { return false; }
	public function isHTML() : bool { return self::$MODE_DETECTED >= 10; }
	public function isJSON() : bool { return self::$MODE_DETECTED === GDT::RENDER_JSON; }
	public function isXML() : bool { return self::$MODE_DETECTED === GDT::RENDER_XML; }
	public function isPDF() : bool { return self::$MODE_DETECTED === GDT::RENDER_PDF; }
	public function isGTK() : bool { return self::$MODE_DETECTED === GDT::RENDER_GTK; }
	# Install / Tests
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }
	# Env
	public static function isDev() : bool { return self::isEnv('dev'); }
	public static function isTes() : bool { return self::isEnv('tes'); }
	public static function isPro() : bool { return self::isEnv('pro'); }
	private static function isEnv(string $env) : bool { return def('GDO_ENV', 'dev') === $env; }
	
	
	/**
	 * Is a session handler supported?
	 */
	public function hasSession() : bool
	{
		return module_enabled('Session');
	}
	
	/**
	 * Call when you create the next command in a loop.
	 * Optionally remove all input, when a form was sent and wants to get cleared.
	 */
	public function reset(bool $removeInput=false) : self
	{
		self::$RESPONSE_CODE = 200;
		GDT_Page::instance()->reset();
		self::$MODE = self::$MODE_DETECTED;
		self::updateTime();
		return $this;
	}
	
	###################
	### Render Mode ###
	###################
	/**
	 * Detect the rendering output mode / format.
	 * GDO Applications currently support 6 formats, a 7th is planned: (GTK+App)
	 */
	public static function detectRenderMode(string $fmt) : int
	{
		switch (strtoupper($fmt))
		{
			case 'CLI': return GDT::RENDER_CLI;
			case 'WS': return GDT::RENDER_BINARY;
			case 'PDF': return GDT::RENDER_PDF;
			case 'JSON': return GDT::RENDER_JSON;
			case 'XML': return GDT::RENDER_XML;
			case 'GTK': return GDT::RENDER_GTK;
			default: return GDT::RENDER_WEBSITE;
		}
	}
	
	/**
	 * Current global rendering mode. @TODO make static for performance-
	 * For example switches from html to cell to form to table etc.
	 */
	public static int $MODE = GDT::RENDER_WEBSITE;
	
	/**
	 * Detected rendering mode for invocation.
	 */
	public static int $MODE_DETECTED = GDT::RENDER_WEBSITE;

	/**
	 * Change current rendering mode.
	 * Optionally set detected mode to this.
	 */
	public function mode(int $mode) : self
	{
		self::$MODE = $mode;
		return $this;
	}
	
	public function modeDetected(int $mode) : self
	{
		self::$MODE_DETECTED = $mode;
		return $this->mode($mode);
	}
	
	############
	### Ajax ###
	############
	/**
	 * Ajax mode is website/html without the html boilerplate.
	 */
	public bool $ajax = false;
	public function ajax(bool $ajax) : self
	{
		$this->ajax = $ajax;
		return $this;
	}
	
	################
	### CLI Mode ###
	################
	/**
	 * Toggle CLI force mode (mostly for tests)
	 */
	public bool $cli = false;
	public function cli(bool $cli=true) : self
	{
		if ($this->cli = $cli)
		{
			return $this->mode(GDT::RENDER_CLI);
		}
		return $this;
	}
	
	##############
	### Themes ###
	##############
	private array $themes;
	
	public function &getThemes() : array
	{
		if (!isset($this->themes))
		{
			$themes = explode(',', def('GDO_THEMES', 'default'));
			$this->themes = array_combine($themes, $themes);
		}
		return $this->themes;
	}
	
	public function hasTheme(string $theme) : bool
	{
		return isset($this->getThemes()[$theme]);
	}
	
	##################
	### JSON Input ###
	##################
	/**
	 * Turn JSON requests into normal Requests.
	 */
	public function handleJSONRequests() : void
	{
		if (isset($_SERVER["CONTENT_TYPE"]) &&
			($_SERVER["CONTENT_TYPE"] === 'application/json'))
		{
			$data = file_get_contents('php://input');
			if ($data = json_decode($data, true))
			{
				$_REQUEST = array_merge($_REQUEST, $data);
			}
		}
	}
	
}

# Init
Application::updateTime();
