<?php
namespace GDO\Core;

/**
 * Add a GDO object attribute to work on to a GDT.
 * This is rarely needed meanwhile, as GDT are filled correctly.
 * 
 * @deprecated A GDT should not require a GDO. 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see GDT
 * @see GDO
 */
trait WithGDO
{
	public GDO $gdo;
	
	/**
	 * Add the current GDO to this GDT.
	 * @param GDO $gdo
	 * @return self
	 */
	public function gdo(GDO $gdo=null) : self
	{
		if ($gdo)
		{
			$this->gdo = $gdo;
		}
		else
		{
			unset($this->gdo);
		}
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
