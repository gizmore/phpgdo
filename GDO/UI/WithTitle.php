<?php
namespace GDO\UI;

/**
 * Adds a title to a GDT.
 * This title is not rendered with a H tag.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.1
 * @see GDT_Headline
 */
trait WithTitle
{
	public $titleKey;
	public $titleArgs;
	public function title(string $key, array $args=null) : self
	{
		$this->titleRaw = null;
	    $this->titleKey = $key;
	    $this->titleArgs = $args;
	    return $this;
	}
	
	public $titleRaw;
	public function titleRaw($title)
	{
	    $this->titleRaw = $title;
	    $this->titleKey = null;
	    $this->titleArgs = null;
	    return $this;
	}

	public $titleEscaped = true;
	public function titleEscaped(bool $escaped)
	{
	    $this->titleEscaped = $escaped;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasTitle() : bool
	{
		return $this->titleKey || $this->titleRaw;
	}
	
	public function renderTitle() : string
	{
		if ($this->titleKey)
		{
			return t($this->titleKey, $this->titleArgs);
		}
		return $this->titleRaw;
	}
	
	public function noTitle() : self
	{
		return $this->titleRaw(null);
	}

}
