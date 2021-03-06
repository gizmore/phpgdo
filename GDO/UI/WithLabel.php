<?php
namespace GDO\UI;

use GDO\Core\WithName;
use GDO\Core\Application;
use GDO\Core\GDT;

/**
 * Add label fields to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.1
 */
trait WithLabel
{
	use WithName;
	
	##############
	### Label ####
	##############
	public ?string $labelRaw = null;
	public ?string $labelKey = null;
	public ?array $labelArgs = null;
	
	public function label(string $key, array $args = null) : self
	{
		$this->labelRaw = null;
		$this->labelKey = $key;
		$this->labelArgs = $args;
		return $this;
	}
	
	public function labelRaw(string $label) : self
	{
		$this->labelRaw = $label;
		$this->labelKey = null;
		$this->labelArgs = null;
		return $this;
	}
	
	public function noLabel() : self
	{
		return $this->labelRaw('');
	}
	
	public function hasLabel() : bool
	{
		return $this->labelKey || $this->labelRaw;
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
	
	private function charRequired() : string
	{
		if (isset($this->notNull) && $this->notNull)
		{
			return '<span class="gdt-required">*</span>';
		}
		return '';
	}
	
	public function renderLabelText() : string
	{
		if ($this->labelKey)
		{
			return t($this->labelKey, $this->labelArgs);
		}
		elseif ($this->labelRaw !== null)
		{
			return $this->labelRaw;
		}
		elseif (isset($this->name))
		{
			return t($this->name);
		}
		else
		{
			return '';
		}
	}
	
	############
	### HTML ###
	############
	/**
	 * HTML string: for="id" 
	 */
	public function htmlForID() : string
	{
		return sprintf(' for="%s"', $this->htmlId());
	}
}
