<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * Add autocompletion variables to a GDT.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.0.1
 */
trait WithCompletion
{
	public string $completionHref;
	public function completionHref(string $completionHref) : self
	{
		$this->completionHref = $completionHref;
		return $this;
	}
	
	public function htmlAutocompleteOff() : string
	{
	    return sprintf('autocomplete="harrambe_%s"', Random::mrandomKey(rand(2,6)));
	}
	
}
