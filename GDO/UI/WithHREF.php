<?php
namespace GDO\UI;

/**
 * Add HTML href capabilities.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
trait WithHREF
{
	public string $href;

	public function href(string $href=null) : self
	{
		if ($href)
		{
			$this->href = $href;
		}
		else
		{
			unset($this->href);
		}
		return $this;
	}

	public function htmlHREF() : string
	{
		if (!isset($this->href))
		{
			return '';
		}
		return sprintf(' href="%s"', html($this->href));
	}

	/**
	 * Replace a get parameter in URL.
	 * Adds if not found
	 */
	public function replacedHREF(string $key, string $value, string $href = null) : string
	{
	    $href = $href === null ? $this->href : $href;
	    
	    $new = "&{$key}=" . urlencode($value);
	    
	    if (strpos($href, "&$key=") !== false)
	    {
	        $key = preg_quote($key);
	        $href = preg_replace("#&{$key}=[^&]+#", $new, $href);
	    }
	    
	    else
	    {
	        $href = $href . $new;
	    }
	    
	    return $href;
	}

}
