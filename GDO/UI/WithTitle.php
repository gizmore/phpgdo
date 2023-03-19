<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * Adds a title to a GDT.
 * This title is not rendered with a H tag.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 * @see GDT_Headline
 */
trait WithTitle
{
	public string $titleRaw;
	public string $titleKey;
	public ?array $titleArgs;
	
	public function title(string $key, array $args=null): static
	{
		unset($this->titleRaw);
	    $this->titleKey = $key;
	    $this->titleArgs = $args;
	    return $this;
	}
	
	public function titleRaw(?string $title): static
	{
		unset($this->titleRaw);
	    unset($this->titleKey);
	    unset($this->titleArgs);
		if ($title)
		{
			$this->titleRaw = $title;
		}
	    return $this;
	}

	public bool $titleEscaped = false;
	public function titleEscaped(bool $escaped): static
	{
	    $this->titleEscaped = $escaped;
	    return $this;
	}
	
	public function noTitle(): static
	{
		unset($this->titleRaw);
		unset($this->titleKey);
		unset($this->titleArgs);
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function hasTitle() : bool
	{
		return isset($this->titleKey) || isset($this->titleRaw);
	}
	
	public function renderTitle() : string
	{
		if (isset($this->titleKey))
		{
			return t($this->titleKey, $this->titleArgs);
		}
		elseif (isset($this->titleRaw))
		{
			return $this->titleRaw;
		}
		else
		{
			return GDT::EMPTY_STRING;
		}
	}
	
}
