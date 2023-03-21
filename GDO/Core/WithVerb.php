<?php
namespace GDO\Core;

/**
 * Add a http verb to a GDT.
 * This is used by Application and GDT_Form.
 * Because traits cannot have constants, GDT_Form holds the GET/POST/OPTIONS/HEAD constants.
 *
 * @author gizmore
 * @see Application
 */
trait WithVerb
{

	public string $verb;

	public function verb(string $verb = null): self
	{
		if ($verb === null)
		{
			unset($this->verb);
		}
		else
		{
			$this->verb = $verb;
		}
		return $this;
	}

	public function htmlVerb(): string
	{
		return isset($this->verb) ? sprintf(' method="%s"', $this->verb) : '';
	}

}
