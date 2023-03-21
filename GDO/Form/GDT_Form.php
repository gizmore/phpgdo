<?php
namespace GDO\Form;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithError;
use GDO\Core\WithFields;
use GDO\Core\WithGDO;
use GDO\Core\WithInput;
use GDO\Core\WithName;
use GDO\Core\WithVerb;
use GDO\Table\GDT_Order;
use GDO\UI\Color;
use GDO\UI\GDT_Repeat;
use GDO\UI\GDT_SearchField;
use GDO\UI\WithPHPJQuery;
use GDO\UI\WithTarget;
use GDO\UI\WithText;
use GDO\UI\WithTitle;

/**
 * A form has a title, a text, fields, menu actions and an http action/target.
 * Can be styled, has a http verb, href and a GDO to operate on.
 * It can be slim, ask for focus and validate it's fields.
 *
 * Quite a biggy!
 *
 * @version 7.0.1
 * @since 3.0.4
 * @author gizmore
 * @see GDT
 * @see MethodForm
 * @see WithText
 * @see WithTitle
 * @see WithFields
 * @see WithAction
 * @see WithActions
 */
final class GDT_Form extends GDT
{

	use WithGDO;
	use WithName;
	use WithText;
	use WithVerb;
	use WithInput;
	use WithTitle;
	use WithError;
	use WithFields;
	use WithTarget;
	use WithAction;
	use WithActions;
	use WithPHPJQuery;

	public const GET = 'get';
	public const POST = 'post';
	public const HEAD = 'head';
	public const OPTIONS = 'options';

	public static ?self $CURRENT = null; # required to handle focus requester engine.
	public bool $slim = false;

	############
	### Slim ###
	############
	public bool $focus = true;

	protected function __construct()
	{
		parent::__construct();
		$this->verb(self::POST);
		$this->addClass('gdt-form');
		$this->action((string)@urldecode($_SERVER['REQUEST_URI']));
	}

	#############
	### Focus ###
	#############

	public function slim(bool $slim = true): self
	{
		$this->slim = $slim;
		return $this;
	}

	public function noFocus(): self
	{
		return $this->focus(false);
	}

	public function focus(bool $focus): self
	{
		$this->focus = $focus;
		return $this;
	}

	###
	public function isEmpty(): bool
	{
		return (!$this->hasFields()) &&
			(!$this->actions()->hasFields());
	}

	##############
	### Render ###
	##############
	public function renderCLI(): string
	{
		if (!$this->hasError())
		{
			$title = $this->renderTitle();
			$text = $this->renderText();
			$tt = trim("{$title} {$text}");
			return $tt;
		}
		else
		{
			$rendered = '';
			foreach ($this->getAllFields() as $gdt)
			{
				if ($gdt->hasError())
				{
					$rendered .= $this->renderCLIError($gdt);
				}
			}
			return $rendered;
		}
	}

	private function renderCLIError(GDT $gdt)
	{
		return t('err_cli_form_gdt', [
				Color::red(html($gdt->getName())),
				html($gdt->renderError())],
			) . "\n";
	}

	public function renderHTML(): string
	{
		$this->addClass($this->slim ? 'gdt-form-slim' : 'gdt-form-compact');

		self::$CURRENT = $this;
		$app = Application::$INSTANCE;
		$old = Application::$MODE;
		$app->mode(GDT::RENDER_FORM);
		$html = GDT_Template::php('Form', 'form_html.php', ['field' => $this]);
		self::$CURRENT = null;
		$app->mode($old);
		return $html;
	}

// 	/**
// 	 * Render html hidden fields for mo/me.
// 	 * @deprecated Feels not nice.
// 	 */
// 	public static function htmlHiddenMoMe() : string
// 	{
// 		return
// 			"<input type=\"hidden\" name=\"_mo\" value=\"{}\" />\n".
// 			"<input type=\"hidden\" name=\"_me\" value=\"{}\" />\n";
// 	}

	################
	### Validate ###
	################
	/**
	 * Validate the form fields.
	 */
	public function validate($value): bool
	{
		$valid = true;
		$inputs = $this->getInputs();
		foreach ($this->getAllFields() as $gdt)
		{
			if (!$gdt->inputs($inputs)->validated())
			{
				$valid = false;
			}
		}
		return $valid ? true : $this->errorFormInvalid();
	}

	public function errorFormInvalid()
	{
		$numErrors = $this->countErrors();
		Application::setResponseCode(GDO_Exception::DEFAULT_ERROR_CODE);
// 		if (Application::instance()->isUnitTests())
// 		{
// 			echo $this->renderCLI();
// 		}
		return $this->error('err_form_invalid', [$numErrors]);
	}

	private function countErrors(): int
	{
		$count = 0;
		foreach ($this->getAllFields() as $gdt)
		{
			$count += $gdt->hasError(); # And this ladies and gentlemen, seems to be the first non branching algo i wrote :)
		}
		return $count;
	}

	###########
	### Var ###
	###########
	public function getFormVar(string $key, bool $throw = true): ?string
	{
		return $this->getField($key, $throw)->getVar();
	}

	public function getFormValue(string $key, bool $throw = true)
	{
		return $this->getField($key, $throw)->getValue();
	}

	/**
	 * Get all columns as gdo var.
	 */
	public function getFormVars(): array
	{
		$back = [];
		foreach ($this->getAllFields() as $gdt)
		{
			if ($gdt instanceof GDT_Repeat)
			{
				$back[$gdt->getName()] = $gdt->getRepeatInput();
			}
			else
			{
				if ($data = $gdt->var($gdt->getVar())->getGDOData())
				{
					$back = array_merge($back, $data);
				}
			}
		}
		return $back;
	}

	############
	### Init ###
	############
	public function initFromGDO(?GDO $gdo): self
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->gdo($gdo);
		}
		return $this;
	}

	###############
	### Display ###
	###############
	/**
	 * Display a label with current filter and order criteria. @TODO: rename method
	 */
	public function displaySearchCriteria(): string
	{
		$data = [];
		foreach ($this->getAllFields() as $gdt)
		{
			if (
				($gdt instanceof GDT_Order) ||
				($gdt instanceof GDT_SearchField)
			)
			{
// 				if (!($var = $gdt->filterVar($this->name)))
				{
					$var = $gdt->getVar();
				}
				if ($var)
				{
					$data[] = sprintf('%s: %s', $gdt->renderLabel(), $gdt->displayVar($var));
				}
			}
		}
		return t('lbl_search_criteria', [implode(', ', $data)]);
	}

}
