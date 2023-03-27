<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\WithName;

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


	public static function renderRequiredIcon(): string
	{
		static $req;
		$req ??= '<span class="gdt-required">' . GDT_Icon::iconS('required', t('required')) . '</span>';
		return $req;
	}


	#############
	### Label ###
	#############


	public string $labelRaw;

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
			$obj->label($name);
		}
		return $obj->defaultLabel();
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


	##############
	### Render ###
	##############


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
		return $this->notNull ? self::renderRequiredIcon() : GDT::EMPTY_STRING;
	}

	public function renderTHead(): string
	{
		return $this->renderLabelText();
	}

	public function labelArgs(?array $args): self
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
		return " for=\"{$this->name}\"";
	}

}
