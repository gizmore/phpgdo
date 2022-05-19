<?php
namespace GDO\UI;

/**
 * Adds a description to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithDescription
{
	public $descrKey;
	public $descrArgs;
	public function description(string $key, array $args=null) : self
	{
		$this->descrRaw = null;
	    $this->descrKey = $key;
	    $this->descrArgs = $args;
	    return $this;
	}
	
	public $descrRaw;
	public function descrRaw($descr)
	{
	    $this->descrRaw = $descr;
	    $this->descrKey = null;
	    $this->descrArgs = null;
	    return $this;
	}

	public $descrEscaped = true;
	public function descrEscaped(bool $escaped)
	{
	    $this->descrEscaped = $escaped;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasDescription() : bool
	{
		return $this->descrKey || $this->descrRaw;
	}
	
	public function renderDescription() : string
	{
		if ($this->descrKey)
		{
			return t($this->descrKey, $this->descrArgs);
		}
		return $this->descrRaw;
	}
	
	public function noDescription() : self
	{
		return $this->descrRaw(null);
	}

}
