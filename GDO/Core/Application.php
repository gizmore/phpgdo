<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use function GDO\Perf\xdebug_get_function_count;
use GDO\DB\Database;

/**
 * Application runtime data.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
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

	################
	### App Time ###
	################
	public static int $TIME;
	public static float $MICROTIME;
	public static function time(float $time)
	{
		self::$TIME = (int)$time;
		self::$MICROTIME = $time;
	}
	
	public static function updateTime()
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
		return self::instance();
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
	public static int $RESPONSE_CODE = 200;
	
	public static function setResponseCode(int $code) : void
	{
		if ($code !== 200)
		{
			self::$RESPONSE_CODE = $code;
		}
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
	public function isAjax() : bool { return $this->ajax; }
	public function isHTML() : bool { return $this->modeDetected === GDT::RENDER_WEBSITE; }
	public function isJSON() : bool { return $this->mode === GDT::RENDER_JSON; }
	public function isXML() : bool { return $this->mode === GDT::RENDER_XML; }
	public function isPDF() : bool { return $this->mode === GDT::RENDER_PDF; }
	public function isCLI() : bool { return $this->cli; }
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }
	public function isWebsocket() : bool { return false; }
	public function isWebserver() : bool { return !$this->cli; }
	public function isAPI() : bool { return !$this->isWebserver(); }

	/**
	 * Is a session handler supported?
	 */
	public function hasSession() : bool { return module_enabled('Session'); }
	
	/**
	 * Call when you create the next command in a loop.
	 * Optionally remove all input, when a form was sent and wants to get cleared.
	 */
	public function reset(bool $removeInput=false) : self
	{
		self::$RESPONSE_CODE = 200;
		GDT_Page::instance()->reset();
		$this->mode = $this->modeDetected;
		self::updateTime();
		if ($removeInput)
		{
			self::$INSTANCE->inputs();
		}
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
	 * Current global rendering mode.
	 * For example switches from html to cell to form to table etc.
	 */
	public int $mode = GDT::RENDER_WEBSITE;
	
	/**
	 * Detected rendering mode for invocation.
	 */
	public int $modeDetected = GDT::RENDER_WEBSITE;

	/**
	 * Change current rendering mode.
	 * Optionally set detected mode to this.
	 */
	public function mode(int $mode, bool $detected=false) : self
	{
		$this->mode = $mode;
		if ($detected)
		{
			$this->modeDetected = $mode;
		}
		return $this;
	}
	
	############
	### Ajax ###
	############
	public bool $ajax = false;
	public function ajax(bool $ajax) : self
	{
		$this->ajax = $ajax;
		return $this;
	}
	
	###########
	### SEO ###
	###########
	public bool $indexed = false;
	public function indexed(bool $indexed=true)
	{
		$this->indexed = $indexed;
		return $indexed;
	}
	
	################
	### CLI Mode ###
	################
	public bool $cli = false;
	public function cli(bool $cli=true)
	{
		if ($this->cli = $cli)
		{
			return $this->mode(GDT::RENDER_CLI, true);
		}
		return $this;
	}
	
	####################
	### Global Input ###
	####################
	public array $inputs;
	public function inputs(array $inputs=null) : self
	{
		$this->inputs = $inputs;
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
	
	public function hasTheme($theme) : bool
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
		if (@$_SERVER["CONTENT_TYPE"] === 'application/json')
		{
			$data = file_get_contents('php://input');
			$data = json_decode($data, true);
			$_REQUEST = array_merge($_REQUEST, $data);
		}
	}
	
	#############
	### Mo/Me ###
	#############
// 	public Method $method;
// 	public string $mo;
// 	public string $me;
	
// 	public function method(Method $method) : self
// 	{
// // 		$this->mo = $method->getModule()->getName();
// // 		$this->me = $method->getName();
// 		$this->method = $method;
// 		return $this;
// 	}
	
// 	public function mo(string $mo) : self
// 	{
// 		$this->mo = strtolower($mo);
// 		return $this;
// 	}
	
// 	public function me(string $me) : self
// 	{
// 		$this->me = strtolower($me);
// 		return $this;
// 	}
	
// 	public function getMoMe() : string
// 	{
// 		$me = $this->method->getMethodName();
// 		$mo = $this->method->getModuleName();
// 		return strtolower("{$mo}::{$me}");
// 	}
	
}

# Init
Application::updateTime();
