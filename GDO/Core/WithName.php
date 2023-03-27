<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Add a name to a GDT.
 * Display human classname.
 * Add trait WithModule.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see WithModule
 */
trait WithName
{

	use WithModule;


	public ?string $name;


	public static function make(string $name = null): static
	{
		return self::makeNamed($name);
	}

	public static function makeNamed(string $name = null): static
	{
		$obj = new static();
		return $obj->name($name ?? $obj->getDefaultName());
	}

	public function getDefaultName(): ?string
	{
		return null;
	}

	public function name(?string $name): self
	{
		$this->name = $name;
		return $this;
	}

	###############
	### Factory ###
	###############

	public function hasName(): bool
	{
		return isset($this->name);
	}

	public function getName(): ?string
	{
		return $this->name ?? null;
	}

}
