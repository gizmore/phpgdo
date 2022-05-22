<?php
namespace GDO\Core;

use GDO\Session\GDO_Session;
use GDO\UI\GDT_Page;

/**
 * Application runtime data.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.0
 */
class Application extends GDT
{
	use WithInstance;

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
	
	public static function runtime() : float
	{
		return microtime(true) - GDO_TIME_START;
	}

	/**
	 * Perf headers as cheap as possible.
	 */
	public static function timingHeader()
	{
		hdr(sprintf('X-GDO-TIME: %.01fms', (microtime(true) - GDO_TIME_START) * 1000.0));
		hdr(sprintf('X-GDO-MEMORY-MAX: %s', memory_get_peak_usage(false)));
		hdr(sprintf('X-GDO-MEMORY-REAL: %s', memory_get_peak_usage(true)));
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
	public function isHTML() : bool { return $this->mode === GDT::RENDER_HTML; }
	public function isJSON() : bool { return $this->mode === GDT::RENDER_JSON; }
	public function isCLI() : bool { return $this->cli; }
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }
	public function isWebserver() : bool { return true; }

	###################
	### Render Mode ###
	###################
	/**
	 * Detect the rendering output mode / format.
	 * Try to append ?_fmt=json on any page.
	 * 
	 * @param string $fmt
	 * @return int
	 */
	public static function detectRenderMode(string $fmt) : int
	{
		switch (strtoupper($fmt))
		{
			case 'CLI': return GDT::RENDER_CLI;
			case 'BWP': return GDT::RENDER_BINARY;
			case 'PDF': return GDT::RENDER_PDF;
			case 'JSON': return GDT::RENDER_JSON;
			case 'XML': return GDT::RENDER_XML;
			default: return GDT::RENDER_HTML;
		}
	}
	
	/**
	 * Call when you create the next command in a loop.
	 */
	public function reset() : void
	{
		self::$RESPONSE_CODE = 200;
		GDT_Page::instance()->reset();
		self::updateTime();
	}
	
	public int $mode = GDT::RENDER_HTML;
	public int $modeDetected = GDT::RENDER_NIL;
	public function mode(int $mode, bool $detected=false) : self
	{
		$this->mode = $mode;
		if ($detected)
		{
			$this->modeDetected = $mode;
		}
		return $this;
	}
	
	public bool $ajax = false;
	public function ajax(bool $ajax) : self
	{
		$this->ajax = $ajax;
		return $this;
	}
	
	public bool $indexed = false;
	public function indexed(bool $indexed=true)
	{
		$this->indexed = $indexed;
		return $indexed;
	}
	
	public bool $cli = false;
	public function cli(bool $cli=true)
	{
		$this->cli = $cli;
		return $this;
	}
	
	##############
	### Themes ###
	##############
	/**
	 * # @TODO: There must be a quicker way todo templating.
	 * @var string[]
	 */
	private array $themes;
	
	public function &getThemes() : array
	{
		if (!isset($this->themes))
		{
			$themes = def('GDO_THEMES', 'default');
			$this->themes = explode(',', $themes);
			$this->themes = array_combine($this->themes, $this->themes);
		}
		return $this->themes;
	}
	
	public function hasTheme($theme) : bool
	{
		return isset($this->getThemes()[$theme]);
	}
	
	/**
	 * Init themes from session settin.
	 * @deprecated do we want this?
	 * @return self
	 */
	public function initThemes() : self
	{
		if ( (!$this->isInstall()) && (!$this->isCLI()) )
		{
			if (class_exists('GDO\\Session\\GDO_Session', false))
			{
				if (GDO_Session::get('theme_name'))
				{
					$this->themes = GDO_Session::get('theme_chain');
				}
			}
		}
		return $this;
	}
	
	##################
	### JSON Input ###
	##################
	/**
	 * Turn JSON requests into normal Requests.
	 * @since 6.11.8
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

Application::updateTime();
