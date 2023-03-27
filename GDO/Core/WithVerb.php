<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Form\GDT_Form;

/**
 * Add a http verb to a GDT.
 * This is used by Application and GDT_Form.
 * Because traits cannot have constants, GDT_Form holds the GET/POST/OPTIONS/HEAD constants.
 *
 * @version 7.0.3
 * @author gizmore
 * @see Application
 * @see GDT_Form
 */
trait WithVerb
{

	public ?string $verb = GDT_Form::GET;

	public function verb(string $verb): self
	{
		$this->verb = $verb;
		return $this;
	}

	public function htmlVerb(): string
	{
		return $this->verb ? " method=\"{$this->verb}\"" : GDT::EMPTY_STRING;
	}

}
