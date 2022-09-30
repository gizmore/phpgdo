<?php
namespace GDO\UI;

use GDO\Core\GDT;

/**
 * HTML target attribute for GDTs.
 * Offers target attribute rendering.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.0
 * @see GDT
 * @see WithHREF
 * @see WithAction
 */
trait WithTarget
{
	public string $target;
	public function target(string $target=null) : self
	{
		if ($target === null)
		{
			unset($this->target);
		}
		else
		{
			$this->target = $target;
		}
		return $this;
	}

	/**
	 * @deprecated Is often blocked on User devices (chrome).
	 */
	public function targetBlank() : self
	{
		return $this->target('_blank');
	}
	
	public function htmlTarget() : string
	{
		if (isset($this->target))
		{
			return sprintf(' target="%s"', $this->target);
		}
		return GDT::EMPTY_STRING;
	}
	
}
