<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Add a GDO object attribute to work on to a GDT.
 * This is rarely needed meanwhile, as GDT are filled correctly.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see GDT
 * @see GDO
 */
trait WithGDO
{

	public GDO $gdo;

	/**
	 * Assign the current GDO to this GDT.
	 * Copy it's data.
	 */
	public function gdo(?GDO $gdo): static
	{
		return $this->gdoVarInitial($gdo);
	}

	protected function gdoVarInitial(GDO $gdo = null, bool $initial = false): static
	{
		$var = $this->initial ?? null;
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

	public function getGDO(): ?GDO
	{
		return $this->gdo ?? null;
	}

	#################
	### Protected ###
	#################

	/**
	 * Assign the current GDO to this GDT. Copy it's data and load it as intial var.
	 */
	public function gdoInitial(GDO $gdo = null): static
	{
		return $this->gdoVarInitial($gdo, true);
	}

}
