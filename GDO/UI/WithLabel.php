<?php
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\WithName;
use GDO\Language\Trans;

/**
 * Add label fields to a GDT.
 *
 * @version 7.0.1
 * @since 5.0.1
 * @author gizmore
 */
trait WithLabel
{

	use WithName;

	private static string $requiredIcon;
	public bool $labelNone = true;

	############
	### Star ###
	############
	public string $labelRaw;
	public string $labelKey;

	#############
	### Label ###
	#############
	public ?array $labelArgs = null;

	public static function make(string $name = null): self
	{
		return self::makeWithLabel($name);
	}

	public static function makeWithLabel(string $name = null): self
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

	public function label(string $key, array $args = null): self
	{
		unset($this->labelRaw);
		$this->labelKey = $key;
		$this->labelArgs = $args;
		return $this->labelNone(false);
	}

	public function labelNone(bool $none = true): self
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

	/**
	 * The label is the label text with the required star asterisk.
	 */
	public function renderLabel(): string
	{
		$text = $this->renderLabelText();
		if (Application::$MODE === GDT::RENDER_FORM)
		{
			$text .= $this->charRequired();
		}
		return $text;
	}

	/**
	 */
	public function renderLabelText(): string
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
	private function charRequired(): string
	{
		return (isset($this->notNull) && ($this->notNull)) ?
			self::renderRequiredIcon() : GDT::EMPTY_STRING;
	}

	public static function renderRequiredIcon(): string
	{
		if (!isset(self::$requiredIcon))
		{
			self::$requiredIcon = '<span class="gdt-required">' . GDT_Icon::iconS('required', t('required')) . '</span>';
		}
		return self::$requiredIcon;
	}

	##############
	### Render ###
	##############

	public function renderTHead(): string
	{
		return $this->renderLabelText();
	}

	public function labelArgs(...$args): self
	{
		$this->labelArgs = $args;
		return $this;
	}

	public function labelRaw(string $label): self
	{
		$this->labelRaw = $label;
		unset($this->labelKey);
		$this->labelArgs = null;
		return $this->labelNone(false);
	}

	############
	### HTML ###
	############

	public function hasLabel(): bool
	{
		return !$this->labelNone;
	}

	##############
	### Render ###
	##############

	/**
	 * HTML string: for="id"
	 */
	public function htmlForID(): string
	{
		return " for=\"{$this->getName()}\"";
	}

}
