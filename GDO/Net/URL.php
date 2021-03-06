<?php
namespace GDO\Net;

use GDO\Core\Application;
use GDO\Util\Regex;

/**
 * This class holds url parts and the raw url.
 * It is the return value of GDT_Url->toValue().
 * 
 * @author gizmore
 * @version 7.0.0
 * @sinve 6.0.2
 * 
 * @see GDT_Url
 */
final class URL
{
    ##############
    ### Static ###
    ##############
	public static function localScheme()
	{
	    if (Application::$INSTANCE->isCLI())
	    {
	        return GDO_PROTOCOL;
	    }
	    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
	}
	
	###############
	### Members ###
	###############
	public $raw;
	public $parts;
	
	public function __construct($url)
	{
		$this->raw = $url;
		$this->parts = parse_url($url);
	}
	
	public function getScheme()
	{
	    return isset($this->parts['scheme']) ? $this->parts['scheme'] : self::localScheme();
	}
	
	public function getHost()
	{
	    if (isset($this->parts['host']))
	    {
	        return $this->parts['host'];
	    }
	    if (isset($this->parts['path']))
	    {
	        return $this->parts['path'];
	    }
	}
	
	public function getPort()
	{
		return $this->parts['port'];
	}
	
	public function getTLD()
	{
	    return Regex::firstMatch('/([^.]+\\.[^.]+)$/ui', $this->getHost());
	}

}
