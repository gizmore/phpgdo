<?php
namespace GDO\Net;

use GDO\Core\GDT_String;
use GDO\Util\Arrays;
use GDO\Core\GDT_Template;
use GDO\UI\WithAnchorRelation;
use GDO\UI\WithTitle;
use GDO\UI\GDT_Link;

/**
 * URL field.
 * Features link checking.
 * Value is a @see URL.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class GDT_Url extends GDT_String
{
    use WithTitle;
    use WithAnchorRelation;
    
    protected function __construct()
    {
    	parent::__construct();
    	$this->icon = 'url';
    }
    
    ##############
    ### Static ###
    ##############
	public static function host() { return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : GDO_DOMAIN; }
	public static function port() { return defined('GDO_PORT') ? GDO_PORT : @$_SERVER['SERVER_PORT']; }
	public static function hostWithPort()
	{
		$port = self::port();
		if (GDO_PROTOCOL === 'https')
		{
			$port = $port == 443 ? '' : ":{$port}";
		}
		else
		{
			$port = $port == 80 ? '' : ":{$port}";
		}
		return self::host() . $port;
	}
	public static function protocol() : string { return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }
	public static function absolute($url) : string { return sprintf('%s://%s%s', self::protocol(), self::hostWithPort(), $url); }
	public static function relative($url) : string { return GDO_WEB_ROOT . $url; }

	public bool $noFollow = false;
	public bool $reachable = false;
	public bool $allowInternal = false;
	public bool $allowExternal = false;
	public array $schemes = ['http', 'https'];
	public int $min = 0;
	public int $max = 767; # Max length for a unique constraint on older mysql systems
	public int $encoding = GDT_String::ASCII;
	
	public function toValue(string $var = null)
	{
		return $var ? new URL($var) : null;
	}
	
	public function toVar($value) : ?string
	{
	    return $value ? $value->raw : null;
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string { return GDT_Template::php('Net', 'cell/url.php', ['field' => $this]); }
	public function displayValue($value) { return GDT_Link::anchor($value->raw, $this->renderLabel()); }
	
	###############
	### Options ###
	###############
	public function allowAll(bool $reachable=true) : self
	{
		$this->allowInternal = true;
		$this->allowExternal = true;
		return $this->reachable($reachable);
	}
	
	public function allowInternal(bool $allowInternal=true) : self
	{
		$this->allowInternal = $allowInternal;
		return $this;
	}
	
	public function allowExternal(bool $allowExternal=true) : self
	{
		$this->allowExternal = $allowExternal;
		return $this;
	}
	
	public function reachable(bool $reachable=true) : self
	{
		$this->reachable = $reachable;
		return $this;
	}
	
	public function schemes(...$schemes)
	{
	    $this->schemes = $schemes;
	    return $this;
	}
	
	public function allSchemes()
	{
	    $this->schemes = null;
	    return $this;
	}
	
// 	public function noFollow(bool $noFollow) : self
// 	{
// 	    $this->noFollow = $noFollow;
// 	    return $this;
// 	}

	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		if (!parent::validate($value?$value->raw:null))
		{
			return false;
		}
		return $this->validateUrl($value);
	}
	
	public function validateUrl(URL $url=null)
	{
		# null allowed by parent validator
	    if ((!$url) || (null === ($value = $url->raw)))
		{
			return true;
		}

		# Check local
		if (!$this->allowInternal)
		{
		    # Check relative url
		    if ($value[0] === '/')
		    {
		        return $this->errorLocal($value);
		    }
		    
		    # Check by IP
		    $ip = gethostbyname($url->getHost());
		    if (GDT_IP::isLocal($ip))
		    {
		        return $this->errorLocal($value);
		    }
		    if ($ip === @$_SERVER['SERVER_ADDR'])
		    {
		        return $this->errorLocal($value);
		    }
		}
		
		if ( (!$this->allowExternal) && ($value[0] !== '/') )
		{
			return $this->error('err_external_url_not_allowed', [html($value)]);
		}
		
		# Check schemes (if external). internal are always prefixed with /
		if ($this->allowExternal)
		{
    		if ($this->schemes && count($this->schemes))
    		{
        		if (!in_array($url->getScheme(), $this->schemes, true))
        		{
        		    return $this->error('err_url_scheme', [Arrays::implodeHuman($this->schemes)]);
        		}
    		}
		}
		
		# Check reachable
		if ($this->reachable)
		{
		    if ($value[0] === '/')
		    {
		        return true; # bailout early
		    }
		    if (!HTTP::pageExists($value))
		    {
    		    return $this->error('err_url_not_reachable', [html($value)]);
		    }
		}
		
		return true;
	}
	
	private function errorLocal($value)
	{
	    return $this->error('err_local_url_not_allowed');
	}
	
	/**
	 * @override
	 */
	public function plugVar() : string
	{
	    if ($this->allowExternal)
	    {
	        return "https://www.wechall.net";
	    }
	    else
	    {
	        return hrefDefault();
	    }
	}

}
