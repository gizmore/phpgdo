<?php
namespace GDO\UI;

use GDO\Core\WithName;
use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Language\Trans;

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
	
	public static function make(string $name = null) : self
	{
		$obj = self::makeNamed($name);
		if ($name = $obj->getName())
		{
			if (Trans::hasKey($name))
			{
				$obj->label($name);
			}
			else
			{
				$obj->defaultLabel();
			}
		}
		else
		{
			$obj->defaultLabel();
		}
		return $obj;
	}
	
	############
	### Star ###
	############
	private static string $requiredIcon;
	public static function renderRequiredIcon() : string
	{
		if (!isset(self::$requiredIcon))
		{
			self::$requiredIcon = '<span class="gdt-required">'.GDT_Icon::iconS('required').'</span>';
		}
		return self::$requiredIcon;
	}
	
	#############
	### Label ###
	#############
	public bool   $labelNone = true;
	public string $labelRaw;
	public string $labelKey;
	public ?array $labelArgs = null;
	
	public function labelNone(bool $none = true) : self
	{
		$this->labelNone = $none;
		if ($none)
		{
			unset($this->labelRaw);
			unset($this->labelKey);
			$this->labelArgs = null;
		}
		return $this;
	}
	
	public function label(string $key, array $args = null) : self
	{
		unset($this->labelRaw);
		$this->labelKey = $key;
		$this->labelArgs = $args;
		return $this->labelNone(false);
	}
	
	public function labelRaw(string $label) : self
	{
		$this->labelRaw = $label;
		unset($this->labelKey);
		$this->labelArgs = null;
		return $this->labelNone(false);
	}
	
	public function hasLabel() : bool
	{
		return !$this->labelNone;
	}
	
	##############
	### Render ###
	##############
	/**
	 * The label is the label text with the required star asterisk.
	 */
	public function renderLabel() : string
	{
		$text = $this->renderLabelText();
		if (Application::$INSTANCE->mode === GDT::RENDER_FORM)
		{
			$text .= $this->charRequired();
		}
		return $text;
	}
	
	/**
	 */
	public function renderLabelText() : string
	{
		if ($this->labelNone)
		{
			return GDT::EMPTY_STRING;
		}
		if (isset($this->labelKey))
		{
			return t($this->labelKey, $this->labelArgs);
		}
		if (isset($this->labelRaw))
		{
			return $this->labelRaw;
		}
		if (isset($this->name))
		{
			return t($this->name);
		}
		return GDT::EMPTY_STRING;
	}
	
	/**
	 * Display the *required* asterisk sign.
	 */
	private function charRequired() : string
	{
		return (isset($this->notNull) && ($this->notNull)) ?
			self::renderRequiredIcon() : GDT::EMPTY_STRING;
	}
	
	############
	### HTML ###
	############
	/**
	 * HTML string: for="id" 
	 */
	public function htmlForID() : string
	{
		return " for=\"{$this->getName()}\"";
	}

}
