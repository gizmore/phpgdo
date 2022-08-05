<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\WithFields;
use GDO\UI\WithTitle;
use GDO\UI\WithText;
use GDO\UI\WithTarget;
use GDO\Core\Application;
use GDO\Core\GDT_Template;
use GDO\Core\WithError;
use GDO\Core\WithInput;
use GDO\Core\WithName;
use GDO\Core\WithVerb;

/**
 * A form has a title, a text, fields, menu actions and an html action/target.
 * 
 * @author gizmore
 * @version 7.0.1
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
	use WithName; # Id
	use WithText; # form info
	use WithVerb; # http request method
	use WithInput; # need input
	use WithTitle; # form title
	use WithError; # form error
	use WithFields; # container
	use WithTarget; # html target
	use WithAction; # html action
	use WithActions; # menu
	
	const GET = 'GET';
	const POST = 'POST';
	const HEAD = 'HEAD';
	const OPTIONS = 'OPTIONS';
	
	public static ?self $CURRENT = null;
	
	protected function __construct()
	{
		parent::__construct();
		$this->verb(self::POST);
	}
	
	##############
	### Inputs ###
	##############
// 	public function inputs(array $inputs) : self
// 	{
// 		$this->addInputs($inputs);
// 		foreach ($this->getAllFields() as $gdt)
// 		{
// 			$gdt->inputs($inputs);
// 		}
// 		$this->actions()->addInputs($inputs);
// 		return $this;
// 	}
	
	public function plugVars() : array
	{
		$back = [];
		foreach ($this->actions()->getAllFields() as $gdt)
		{
			$name = $gdt->getName();
			$back[$name] = '1';
		}
		return array_values($back);
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
	
	#############
	### Focus ###
	#############
	public bool $focus = true;
	public function noFocus() : self
	{
		return $this->focus(false);
	}
	public function focus(bool $focus) : self
	{
		$this->focus = $focus;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderCLI() : string
	{
		$title = $this->renderTitle();
		$text = $this->renderText();
		return $title . ' ' . $text;
	}

	public function renderCell() : string
	{
		self::$CURRENT = $this;
		$app = Application::$INSTANCE;
		$old = $app->mode;
		$app->mode(GDT::RENDER_FORM);
		$html = GDT_Template::php('Form', 'form_html.php', ['field' => $this]);
		self::$CURRENT = null;
		$app->mode($old);
		return $html;
	}
	
	public function htmlID() : string
	{
		if ($name = $this->getName())
		{
			return sprintf(' id="form_%s"', $name);
		}
		return '';
	}
	
	public static function htmlHiddenMoMe() : string
	{
		return
		"<input type=\"hidden\" name=\"_mo\" value=\"{}\" />\n".
		"<input type=\"hidden\" name=\"_me\" value=\"{}\" />\n";
	}
	
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		$valid = true;
		foreach ($this->getAllFields() as $key => $gdt)
		{
// 			if ($gdt->hasInputs())
			{
				$input = $gdt->getInput($key);
				if (!$gdt->validateInput($input, false))
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
	
	###########
	### Var ###
	###########
	public function getFormVar(string $key, bool $throw=true) : string
	{
		return $this->getField($key, $throw)->getVar();
	}
	
	public function getFormValue(string $key, bool $throw=true)
	{
		return $this->getField($key, $throw)->getValue();
	}

	/**
	 * Get all columns as gdo var.
	 * @return array
	 */
	public function getFormVars() : array
	{
		$back = [];
		foreach ($this->getAllFields() as $gdt)
		{
			if ($data = $gdt->getGDOData())
			{
				$back = array_merge($back, $data);
			}
		}
		return $back;
	}
	
	##############
	### Fields ###
	##############
	public function removeFormField(string $key) : self
	{
		return $this->removeField($this->getField($key));
	}
	
}
