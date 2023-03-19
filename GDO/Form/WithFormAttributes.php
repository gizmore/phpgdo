<?php
namespace GDO\Form;

/**
 * Add HTML Form helpers to a GDT.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see GDT_Field
 * @see WithPHPJQuery
 */
trait WithFormAttributes
{
	public bool $hidden = false;
	public function hidden(bool $hidden = true): static { $this->hidden = $hidden; return $this; }
	public function isHidden() : bool { return $this->hidden; }
	
	public bool $readable = true;
	public function readable(bool $readable): static { $this->readable = $readable; return $this; }
	public function isReadable() : bool { return $this->readable; }
	
	public bool $writeable = true;
	public function writeable(bool $writeable): static { $this->writeable = $writeable; return $this; }
	public function isWriteable() : bool { return $this->writeable; }
	
	public bool $focusable = true;
	public function focusable(bool $focusable): static { $this->focusable = $focusable; return $this; }
	public function isFocusable() : bool { return $this->focusable; }
	
	################
	### Disabled ###
	################
	public function enabled(bool $enabled=true): static
	{
		$this->writeable = $enabled;
		return $this;
	}
	
	public function disabled(bool $disabled=true): static
	{
		$this->writeable = !$disabled;
		return $this;
	}
	
	public function isDisabled() : bool
	{
		return !$this->writeable;
	}
	
	public function htmlDisabled() : string
	{
		return $this->isDisabled() ? ' disabled="disabled"' : '';
	}
	
	##############
	### Render ###
	##############
	public function formVariable() : string
	{
		return $this->hasName() ? $this->getName() : '';
	}

}
