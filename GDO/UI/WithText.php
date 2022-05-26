<?php
namespace GDO\UI;

/**
 * Adds text attributes.
 * 
 * Adds text($key, $args) for I18n version
 * Adds textRaw($text) for raw version
 * Adds textUnescaped() for skipping escaping.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 * @see GDO7 - for global functions
 */
trait WithText
{
	public string $textRaw;
	public string $textKey;
	public ?array $textArgs;
	public bool $textEscaped = false;
	
	public function text(string $key, array $args=null) : self
	{
		unset($this->textRaw);
	    $this->textKey = $key;
	    $this->textArgs = $args;
	    return $this;
	}
	
	public function textRaw(string $text) : self
	{
		$this->textRaw = $text;
		unset($this->textKey);
		unset($this->textArgs);
	    return $this->textUnescaped();
	}

	public function textNone() : self
	{
		unset($this->textRaw);
		unset($this->textKey);
		unset($this->textArgs);
		return $this;
	}
	
	public function textEscaped(bool $escaped) : self
	{
	    $this->textEscaped = $escaped;
	    return $this;
	}
	
	public function textUnescaped(bool $unescaped=true) : self
	{
		return $this->textEscaped(!$unescaped);
	}
	
	##############
	### Render ###
	##############
	public function hasText() : bool
	{
		return isset($this->textKey) || isset($this->textRaw);
	}
	
	public function renderText() : string
	{
		if (isset($this->textKey))
		{
			$txt = t($this->textKey, $this->textArgs);
		}
		elseif (isset($this->textRaw))
		{
			$txt = $this->textRaw;
		}
		else
		{
			return '';
		}
		return $this->textEscaped ? html($txt) : $txt;
	}
	
}
