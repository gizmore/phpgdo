<?php
namespace GDO\Core;

/**
 * Add a GDO object attribute to work on to a GDT.
 * This is rarely needed meanwhile, as GDT are filled correctly.
 * 
 * @author gizmore
 * @version 7.0.1
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
			if ($name = $this->getName())
			{
				$var = $gdo->gdoVar($name);
				return $this->var($var);
			}
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
