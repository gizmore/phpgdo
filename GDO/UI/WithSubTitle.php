<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds a subtitle to a GDT.
 * This subtitle is not rendered with a H tag.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see WithTitle
 * @see GDT_Title
 * @see GDT_Headline
 */
trait WithSubTitle
{
	public string $subtitleRaw;
	public string $subtitleKey;
	public ?array $subtitleArgs;
	public function subtitle(string $key, array $args=null) : self
	{
		unset($this->subtitleRaw);
		$this->subtitleKey = $key;
		$this->subtitleArgs = $args;
	    return $this;
	}
	
	public function subtitleRaw(?string $subtitle) : self
	{
		unset($this->subtitleRaw);
		if ($subtitle)
		{
			$this->subtitleRaw = $subtitle;
		}
		unset($this->subtitleKey);
		unset($this->subtitleArgs);
	    return $this;
	}

	public function noSubTitle() : self
	{
		unset($this->subtitleRaw);
		unset($this->subtitleKey);
		unset($this->subtitleArgs);
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
		return isset($this->subtitleKey) || isset($this->subtitleRaw);
	}
	
	public function renderSubTitle() : string
	{
		if (isset($this->subtitleKey))
		{
			return t($this->subtitleKey, $this->subtitleArgs);
		}
		elseif (isset($this->subtitleRaw))
		{
			return $this->subtitleRaw;
		}
		else
		{
			return GDT::EMPTY_STRING;
		}
	}
	
}
