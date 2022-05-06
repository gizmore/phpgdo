<?php
namespace GDO\UI;

/**
 * Adds a subtitle to a GDT.
 * This subtitle is not rendered with a H tag.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see GDT_Headline
 */
trait WithSubTitle
{
	public string $subtitleKey;
	public array $subtitleArgs;
	public function subtitle(string $key, array $args=null) : self
	{
		$this->subtitleRaw = null;
		$this->subtitleKey = $key;
		$this->subtitleArgs = $args;
	    return $this;
	}
	
	public string $subtitleRaw;
	public function subtitleRaw($title)
	{
		$this->subtitleRaw = $title;
		$this->subtitleKey = null;
		$this->subtitleArgs = null;
	    return $this;
	}

	public $subtitleEscaped = false;
	public function subtitleEscaped(bool $escaped=true)
	{
		$this->subtitleEscaped = $escaped;
	    return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasSubTitle() : bool
	{
		return $this->subtitleKey || $this->subtitleRaw;
	}
	
	public function renderSubTitle() : string
	{
		if ($this->subtitleKey)
		{
			return t($this->subtitleKey, $this->subtitleArgs);
		}
		return $this->subtitleRaw;
	}
	
	public function noSubTitle() : self
	{
		return $this->subtitleRaw(null);
	}

}
