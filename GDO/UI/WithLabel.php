<?php
namespace GDO\UI;

use GDO\Core\WithName;
use GDO\Core\Application;
use GDO\Core\GDT;

/**
 * Add label fields to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.1
 */
trait WithLabel
{
	use WithName;
	
	public string $labelRaw;
	public string $labelKey;
	public ?array $labelArgs = null;
	
	public function label(string $key, array $args = null) : self
	{
		unset($this->labelRaw);
		$this->labelKey = $key;
		$this->labelArgs = $args;
		return $this;
	}
	
	public function labelRaw(string $label) : self
	{
		$this->labelRaw = $label;
		unset($this->labelKey);
		$this->labelArgs = null;
		return $this;
	}
	
	public function noLabel() : self
	{
		unset($this->labelRaw);
		unset($this->labelKey);
		$this->labelArgs = null;
		return $this;
	}
	
	public function hasLabel() : bool
	{
		return isset($this->labelKey) || isset($this->labelRaw);
	}
	
	##############
	### Render ###
	##############
	public function renderLabel() : string
	{
		$text = $this->renderLabelText();
		if (Application::$INSTANCE->mode === GDT::RENDER_FORM)
		{
			$text .= $this->charRequired();
		}
		return $text;
	}
	
	public function renderLabelText() : string
	{
		if (isset($this->labelKey))
		{
			return t($this->labelKey, $this->labelArgs);
		}
		elseif (isset($this->labelRaw))
		{
			return $this->labelRaw;
		}
		elseif (isset($this->name))
		{
			return t($this->name);
		}
		else
		{
			return GDT::EMPTY_STRING;
		}
	}
	
	/**
	 * Display the required asterisk sign.
	 */
	private function charRequired() : string
	{
		if (isset($this->notNull) && $this->notNull)
		{
			return '<span class="gdt-required">*</span>';
		}
		return '';
	}
	
	############
	### HTML ###
	############
	/**
	 * HTML string: for="id" 
	 */
	public function htmlForID() : string
	{
		return " for=\"{$this->name}\"";
	}

}
