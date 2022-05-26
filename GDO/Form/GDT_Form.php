<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\WithText;
use GDO\UI\WithTarget;
use GDO\Core\GDT_Template;
use GDO\Core\WithError;
use GDO\Core\WithInput;

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
	use WithInput; # need input
	use WithTitle; # form title
	use WithError; # form error
	use WithFields; # container
	use WithTarget; # html target
	use WithAction; # html action
	use WithActions; # menu
	
	##############
	### Inputs ###
	##############
	public function inputs(array $inputs) : self
	{
		$this->inputs = $inputs;
		$this->actions()->inputs($inputs);
		return $this;
	}
	
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
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return GDT_Template::php('Form', 'form_html.php', ['field' => $this]);
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		$valid = true;
		foreach ($this->getAllFields() as $key => $gdt)
		{
			if ($gdt->hasInputs())
			{
				$input = $this->getInput($key);
				if (!$gdt->validateInput($input))
				{
					$valid = false;
				}
			}
		}
		return $valid ? true : $this->errorFormInvalid();
	}

	public function errorFormInvalid()
	{
		$numErrors = $this->countErrors();
		return $this->error('err_form_invalid', [$numErrors]);
	}
	
	private function countErrors() : int
	{
		$count = 0;
		foreach ($this->getAllFields() as $gdt)
		{
			if ($gdt->hasError())
			{
				$count++;
			}
		}
		return $count;
	}

}
