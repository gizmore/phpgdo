<?php
namespace GDO\UI;

/**
 * WithText GDT trait.
 * 
 * Adds $text attribute.
 * Adds text($key, $args) for I18n version
 * Adds textRaw($s) for raw version
 * Adds textUnescaped() for skipping escaping.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 * @see GDO7
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
	public function textRaw(string $text) : self
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
	public function textUnescaped(bool $unescaped=true) : self
	{
		return $this->textEscaped(!$unescaped);
	}
	
	public function noText() : self
	{
		unset($this->textRaw);
		unset($this->textKey);
		unset($this->textArgs);
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
	
}
