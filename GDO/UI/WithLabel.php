<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\WithName;
use GDO\Language\Trans;

/**
 * Add label fields to a GDT.
 *
 * @version 7.0.3
 * @since 5.0.1
 * @author gizmore
 */
trait WithLabel
{

	use WithName;


	public string $labelRaw;


	#############
	### Label ###
	#############
	public string $labelKey;
	public ?array $labelArgs;
	public bool $labelNone = true;

	public static function make(string $name = null): static
	{
		return self::makeWithLabel($name);
	}

	public static function makeWithLabel(string $name = null): static
	{
		$obj = self::makeNamed($name);
        if ($name)
        {
            return $obj->label($name);
        }
		elseif ($name = $obj->getDefaultName())
		{
			return $obj->label($name);
		}
        else
        {
            return $obj->defaultLabel();
        }
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

	public function labelArgs(?array $args): self
	{
		$this->labelArgs = $args;
		return $this;
	}


	##############
	### Render ###
	##############

	public function labelRaw(string $label): self
	{
		$this->labelRaw = $label;
		unset($this->labelKey);
		$this->labelArgs = null;
		return $this->labelNone(false);
	}

	/**
	 * HTML string: for="id"
	 */
	public function htmlForID(): string
	{
		return " for=\"{$this->name}\"";
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
		if (isset($this->labelKey))
		{
			return t($this->labelKey, $this->labelArgs);
		}
		if ($this->labelNone)
		{
			return GDT::EMPTY_STRING;
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
		return $this->isRequired() ? self::renderRequiredIcon() : GDT::EMPTY_STRING;
	}

	public static function renderRequiredIcon(): string
	{
		static $req;
		$req ??= '<span class="gdt-required">' . GDT_Icon::iconS('required', t('required')) . '</span>';
		return $req;
	}

	############
	### HTML ###
	############

	public function renderTHead(): string
	{
		return $this->renderLabelText();
	}

	##############
	### Render ###
	##############

	public function hasLabel(): bool
	{
		if (isset($this->labelRaw))
		{
			return true;
		}
		if (isset($this->labelKey) && Trans::hasKey($this->labelKey))
		{
			return true;
		}
		return !$this->labelNone;
	}

}
