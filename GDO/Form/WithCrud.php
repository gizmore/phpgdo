<?php
namespace GDO\Form;

/**
 * Add 4 CRUD boolean flags to a GDT.
 *
 * @version 7.0.0
 * @since 6.11.4
 * @author gizmore
 */
trait WithCrud
{

	public bool $creatable = false;
	public bool $readable = false;
	public bool $updatable = false;
	public bool $deletable = false;

	public function creatable($creatable = true)
	{
		$this->creatable = $creatable;
		return $this;
	}

	public function readable($readable = true)
	{
		$this->readable = $readable;
		return $this;
	}

	public function updatable($updatable = true)
	{
		$this->updatable = $updatable;
		return $this;
	}

	public function deletable($deletable = true)
	{
		$this->deletable = $deletable;
		return $this;
	}

}
