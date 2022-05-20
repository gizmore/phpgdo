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
	public function isInstall() : bool { return false; }
	public function isUnitTests() : bool { return false; }
	public function getAjax() : string { return isset($_REQUEST['_ajax']) ? $_REQUEST['_ajax'] : '0'; }
	public function isFormat(string $format) : bool { return $this->getFormat() === $format; }
	public function getFormat() : string { return isset($_REQUEST['_fmt']) ? $_REQUEST['_fmt'] : 'html'; }
	
	/**
	 * Call when you create the next command in a loop.
	 */
	public function reset()
	{
		Application::$RESPONSE_CODE = 200;
		$_REQUEST = $_GET = $_POST = [];
		GDT_Page::instance()->reset();
		self::updateTime();
	}
	
	##############
	### Themes ###
	##############
	private array $themes;
	public function getThemes()
	{
		if (!isset($this->themes))
		{
			$themes = def('GDO_THEMES', 'default');
			$this->themes = explode(',', $themes);
			$this->themes = array_combine($this->themes, $this->themes);
		}
		return $this->themes;
	}
	
	public function hasTheme($theme) { return isset($this->getThemes()[$theme]); }
	public function initThemes()
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
	
}

Application::updateTime();
