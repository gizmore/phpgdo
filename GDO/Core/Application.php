<?php
namespace GDO\Core;

use GDO\Session\GDO_Session;
use GDO\UI\GDT_Page;

/**
 * Application runtime data.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 3.0.0
 */
class Application
{
	###################
	### Instanciate ###
	###################
	private static Application $INSTANCE;
	
	public static function instance() : self
	{
		return self::$INSTANCE;
	}
	
	public function __construct()
	{
		self::$INSTANCE = $this;
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
	
	public static function timingHeader()
	{
		hdr(sprintf('X-GDO-TIME: %.04f', (microtime(true) - GDO_PERF_START)));
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
	public function isCLI() : bool { return false; }
	public function isAjax() : bool { return !!$this->getAjax(); }
	public function isJSON() : bool { return $this->isFormat('json'); }
	public function isHTML() : bool { return $this->isFormat('html'); }
	public function isWebserver() : bool { return true; }
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }
	public function getAjax() : string { return isset($_REQUEST['_ajax']) ? $_REQUEST['_ajax'] : '0'; }
	public function isFormat(string $format) : bool { return $this->getFormat() === $format; }
	public function getFormat() : string { return isset($_REQUEST['_fmt']) ? $_REQUEST['_fmt'] : 'html'; }
	
	/**
	 * Call when you create the next command in a loop.
	 */
	public function reset() : void
	{
		self::$RESPONSE_CODE = 200;
		GDT_Page::instance()->reset();
		self::updateTime();
	}
	
	##############
	### Themes ###
	##############
	/**
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
