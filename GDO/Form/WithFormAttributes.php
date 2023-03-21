<?php
namespace GDO\Form;

/**
 * Add HTML Form helpers to a GDT.
 *
 * @version 7.0.1
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

	public function hidden(bool $hidden = true): self
	{
		$this->hidden = $hidden;
		return $this;
	}

	public function readable(bool $readable): self
	{
		$this->readable = $readable;
		return $this;
	}

	public function focusable(bool $focusable): self
	{
		$this->focusable = $focusable;
		return $this;
	}

	public function isFocusable(): bool { return $this->focusable; }

	public function enabled(bool $enabled = true): self
	{
		$this->writeable = $enabled;
		return $this;
	}

	public function disabled(bool $disabled = true): self
	{
		$this->writeable = !$disabled;
		return $this;
	}

	public function isHidden(): bool { return $this->hidden; }

	public function isReadable(): bool { return $this->readable; }

	################
	### Disabled ###
	################

	public function writeable(bool $writeable): self
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
		return $this->hasName() ? $this->getName() : '';
	}

}
