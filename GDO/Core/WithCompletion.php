<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * Add autocompletion attributes to a GDT.
 * 
 *  - Turns the browser's autocompletion off via 'autocomplete="rand()"'
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.3.5
 */
trait WithCompletion
{
	############
	### HREF ###
	############
	public string $completionHref;
	
	public function completionHref(string $completionHref): static
	{
		$this->completionHref = $completionHref;
		return $this;
	}
	
	public function noCompletion(): static
	{
		unset($this->completionHref);
		return $this;
	}
	
	public function hasCompletion() : bool
	{
		return isset($this->completionHref);
	}
	
	##############
	### Render ###
	##############
	public function htmlAutocompleteOff() : string
	{
	    return sprintf(' autocomplete="harrambe_%s"', Random::mrandomKey(rand(2,6)));
	}
	
}
