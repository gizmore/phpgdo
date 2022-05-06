<?php
namespace GDO\Core;

/**
 * Application runtime data.
 * 
 * @author gizmore
 * @version 7.0.0
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
	
	############
	### Time ###
	############
	public static int $TIME;
	public static float $TIME_F;
	public static function time(float $time)
	{
		self::$TIME = (int)$time;
		self::$TIME_F = $time;
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
	public function isUnitTests() : bool { return false; }
	public function getAjax() : string { return isset($_REQUEST['_ajax']) ? $_REQUEST['_ajax'] : '0'; }
	public function isFormat(string $format) : bool { return $this->getFormat() === $format; }
	public function getFormat() : string { return isset($_REQUEST['_fmt']) ? $_REQUEST['_fmt'] : 'html'; }
	
}

Application::time(microtime(true));
