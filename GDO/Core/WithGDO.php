<?php
namespace GDO\Core;

/**
 * Add a GDO object to work on to a GDT.
 * 
 * @see GDT
 * @see GDO
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithGDO
{
	public GDO $gdo;
	
	public function gdo(GDO $gdo) : self
	{
		$this->gdo = $gdo;
		return $this;
	}
	
	public function hasGDO() : bool
	{
		return !!$this->gdo;
	}
	
	public function getGDO() : GDO
	{
		return $this->gdo;
	}
	
}
