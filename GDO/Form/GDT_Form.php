<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\WithText;
use GDO\UI\WithTarget;

/**
 * A form has a title, a text, fields, menu actions and an html action/target.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.4
 * @see MethodForm
 * @see WithText
 * @see WithTitle
 * @see WithFields
 * @see WithAction
 * @see WithActions
 */
final class GDT_Form extends GDT
{
	use WithText; # form info
	use WithTitle; # form title
	use WithFields; # container
	use WithTarget; # html target
	use WithAction; # html action
	use WithActions; # menu
	
	############
	### Verb ###
	############
	const GET = 'GET';
	const POST = 'POST';
	public string $verb = self::POST;
	public function verb(string $verb) : self
	{
		$this->verb = $verb;
		return $this;
	}

	############
	### Slim ###
	############
	public bool $slim = false;
	public function slim(bool $slim=true) : self
	{
		$this->slim = $slim;
		return $this;
	}
	

}
