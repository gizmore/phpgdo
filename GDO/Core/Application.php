<?php
namespace GDO\Core;

use GDO\UI\GDT_Page;
use function GDO\Perf\xdebug_get_function_count;
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
// 	public static array $HREFS = []; # @TODO In UnitTest, collect all calls to href(). Click every generated link :)

	################
	### App Time ###
	################
	public static int $TIME;
	public static float $MICROTIME;
	public static function time(float $time) : void
	{
		self::$TIME = (int)$time;
		self::$MICROTIME = $time;
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
		# Could init stuff here? ... meh
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
	/**
	 * HTTP Response Code.
	 */
	public static int $RESPONSE_CODE = 200;
	
	/**
	 * Set the HTTP response code.
	 */
	public static function setResponseCode(int $code) : void
	{
		if ($code !== 200)
		{
// 			if (self::$RESPONSE_CODE !== 200)
// 			{
				self::$RESPONSE_CODE = $code;
// 			}
		}
// 		if ($code >= 400)
// 		{
// 			if (defined('GDO_CORE_STABLE'))
// 			{
// 				xdebug_break();
// 			}
// 		}
// 		if ($code >= 500)
// 		{
// 			if (defined('GDO_CORE_STABLE'))
// 			{
// 				xdebug_break();
// 			}
// 		}
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
	public function isWebserver() : bool { return !$this->isCLI(); }
	# Render
	public function isCLI() : bool { return $this->cli; }
	public function isAjax() : bool { return $this->ajax; }
	public function isWebsocket() : bool { return false; }
	public function isHTML() : bool { return $this->modeDetected >= 10; }
	public function isJSON() : bool { return $this->modeDetected === GDT::RENDER_JSON; }
	public function isXML() : bool { return $this->modeDetected === GDT::RENDER_XML; }
	public function isPDF() : bool { return $this->modeDetected === GDT::RENDER_PDF; }
	public function isGTK() : bool { return $this->modeDetected === GDT::RENDER_GTK; }
	# Install / Tests
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }

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
		$this->mode = $this->modeDetected;
		if ($removeInput)
		{
			$this->inputs();
		}
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
	/**
	 * Ajax mode is website/html without the html boilerplate.
	 */
	public bool $ajax = false;
	public function ajax(bool $ajax) : self
	{
		$this->ajax = $ajax;
		return $this;
	}
	
	###########
	### SEO ###
	###########
	/**
	 * Toggle if this page should be indexed by search engines.
	 */
	public bool $indexed = false;
	public function indexed(bool $indexed=true)
	{
		$this->indexed = $indexed;
		return $indexed;
	}
	
	################
	### CLI Mode ###
	################
	/**
	 * Toggle CLI force mode (mostly for tests)
	 */
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
	/**
	 * Current input
	 * @deprecated
	 * @var string[string]
	 */
	public array $inputs;
	
	/**
	 * @deprecated
	 */
	public function inputs(array $inputs=null) : self
	{
		if ($inputs === null)
		{
			unset($this->inputs);
		}
		else
		{
			$this->inputs = $inputs;
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
	
}

# Init
Application::updateTime();
