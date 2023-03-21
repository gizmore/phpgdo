<?php
namespace GDO\UI;

/**
 * Adds a description to a GDT.
 *
 * @version 7.0.0
 * @since 7.0.0
 * @author gizmore
 */
trait WithDescription
{

	public $descrKey;
	public $descrArgs;
	public $descrRaw;
	public $descrEscaped = true;

	public function description(string $key, array $args = null): self
	{
		$this->descrRaw = null;
		$this->descrKey = $key;
		$this->descrArgs = $args;
		return $this;
	}

	public function descrEscaped(bool $escaped)
	{
		$this->descrEscaped = $escaped;
		return $this;
	}

	public function hasDescription(): bool
	{
		return $this->descrKey || $this->descrRaw;
	}

	##############
	### Render ###
	##############

	public function renderDescription(): string
	{
		if ($this->descrKey)
		{
			return t($this->descrKey, $this->descrArgs);
		}
		return $this->descrRaw;
	}

	public function noDescription(): self
	{
		return $this->descrRaw(null);
	}

	public function descrRaw($descr)
	{
		$this->descrRaw = $descr;
		$this->descrKey = null;
		$this->descrArgs = null;
		return $this;
	}

}
