<?php
namespace GDO\Form;

/**
 * Add 4 CRUD boolean flags to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.11.4
 */
trait WithCrud
{
	public bool $creatable = false;
	public function creatable($creatable=true)
	{
		$this->creatable = $creatable;
		return $this;
	}
	
	public bool $readable = false;
	public function readable($readable=true)
	{
		$this->readable = $readable;
		return $this;
	}
	
	public bool $updatable = false;
	public function updatable($updatable=true)
	{
		$this->updatable = $updatable;
		return $this;
	}
	
	public bool $deletable = false;
	public function deletable($deletable=true)
	{
		$this->deletable = $deletable;
		return $this;
	}
	
}
