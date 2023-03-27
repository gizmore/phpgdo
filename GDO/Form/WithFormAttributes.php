<?php
declare(strict_types=1);
namespace GDO\Form;

/**
 * Add HTML Form helpers to a GDT.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see GDT_Field
 * @see WithPHPJQuery
 */
trait WithFormAttributes
{

	public bool $hidden = false;
	public bool $readable = true;
	public bool $writeable = true;
	public bool $focusable = true;

	public function hidden(bool $hidden = true): static
	{
		$this->hidden = $hidden;
		return $this;
	}

	public function readable(bool $readable): static
	{
		$this->readable = $readable;
		return $this;
	}

	public function focusable(bool $focusable): static
	{
		$this->focusable = $focusable;
		return $this;
	}

	public function isFocusable(): bool { return $this->focusable; }

	public function enabled(bool $enabled = true): static
	{
		$this->writeable = $enabled;
		return $this;
	}

	public function disabled(bool $disabled = true): static
	{
		return $this->enabled(!$disabled);
	}

	public function isHidden(): bool { return $this->hidden; }

	public function isReadable(): bool { return $this->readable; }

	################
	### Disabled ###
	################

	public function writeable(bool $writeable): static
	{
		$this->writeable = $writeable;
		return $this;
	}

	public function isWriteable(): bool { return $this->writeable; }

	public function isDisabled(): bool
	{
		return !$this->writeable;
	}

	public function htmlDisabled(): string
	{
		return $this->isDisabled() ? ' disabled="disabled"' : '';
	}

	##############
	### Render ###
	##############
	public function formVariable(): string
	{
		return (string) $this->getName();
	}

}
