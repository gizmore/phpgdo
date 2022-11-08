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
	
	public function isTestable(): bool
	{
		return false;
	}
	
	/**
	 * Assign the current GDO to this GDT. Copy it's data.
	 */
	public function gdo(GDO $gdo = null) : self
	{
		return $this->gdoVarInitial($gdo, false);
	}
	
	public function getGDO(): ?GDO
	{
		return isset($this->gdo) ? $this->gdo : null;
	}
	
	/**
	 * Assign the current GDO to this GDT. Copy it's data and load it as intial var.
	 */
	public function gdoInitial(GDO $gdo = null) : self
	{
		return $this->gdoVarInitial($gdo, true);
	}

	#################
	### Protected ###
	#################
	protected function gdoVarInitial(GDO $gdo = null, bool $initial = false)
	{
		$var = isset($this->initial) ? $this->initial : null;
		if ($gdo)
		{
			$this->gdo = $gdo;
			if ($name = $this->getName())
			{
				if ($gdo->hasVar($name))
				{
					$var = $gdo->gdoVar($name);
				}
			}
		}
		else
		{
			unset($this->gdo);
		}
		return $initial ? $this->initial($var) : $this->var($var);
	}
	
}
