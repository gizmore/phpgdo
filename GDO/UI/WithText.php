<?php
namespace GDO\UI;

/**
 * Adds a text to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 */
trait WithText
{
	public $textKey;
	public $textArgs;
	public function text(string $key, array $args=null) : self
	{
		$this->textRaw = null;
	    $this->textKey = $key;
	    $this->textArgs = $args;
	    return $this;
	}
	
	public $textRaw;
	public function textRaw($text) : self
	{
	    $this->textRaw = $text;
	    $this->textKey = null;
	    $this->textArgs = null;
	    return $this;
	}

	public $textEscaped = true;
	public function textEscaped(bool $escaped) : self
	{
	    $this->textEscaped = $escaped;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasText() : bool
	{
		return $this->textKey || $this->textRaw;
	}
	
	public function renderText() : string
	{
		$t = $this->textRaw;
		if ($this->textKey)
		{
			$t = t($this->textKey, $this->textArgs);
		}
		if ($this->textEscaped)
		{
			$t = html($t);
		}
		return $t;
	}
	
	public function noText() : self
	{
		return $this->textRaw(null);
	}

}
