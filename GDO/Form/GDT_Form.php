<?php
declare(strict_types=1);
namespace GDO\Form;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\Website;
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
 * @version 7.0.3
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

	final public const GET = 'GET';

	final public const POST = 'POST';

	final public const HEAD = 'HEAD';

	final public const OPTIONS = 'OPTIONS';

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
		$this->action(urldecode($_SERVER['REQUEST_URI']));
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
			return trim("{$title} {$text}");
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

	private function renderCLIError(GDT $gdt): string
	{
		return t('err_cli_form_gdt', [
				Color::red(html($gdt->getName())),
				html($gdt->renderError())],
			);
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

	################
	### Validate ###
	################
	/**
	 * Validate the form fields.
	 *
	 * @throws GDO_ArgError
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
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
		return $valid || $this->errorFormInvalid();
	}

	public function errorFormInvalid(): bool
	{
		$numErrors = $this->countErrors();
//		Application::setResponseCode(GDO_Exception::DEFAULT_ERROR_CODE);
		$errors = $this->renderError();
		Website::errorRaw($this->getModule()->gdoHumanName(), $errors);
		return $this->error('err_form_invalid', [$numErrors, $errors]);
	}

	private function countErrors(): int
	{
		$count = 0;
		foreach ($this->getAllFields() as $gdt)
		{
			$count += $gdt->hasError();
		}
		return $count;
	}

	###########
	### Var ###
	###########
	public function getFormVar(string $key): ?string
	{
		return $this->getField($key)->getVar();
	}

	public function getFormValue(string $key): mixed
	{
		return $this->getField($key)->getValue();
	}

	/**
	 * Get all columns as gdo var.
	 */
	public function getFormVars(): array
	{
		$back = [];
		$inputs = $this->getInputs();
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->inputs($inputs);
			if ($gdt instanceof GDT_Repeat)
			{
				$back[$gdt->getName()] = $gdt->getRepeatInput();
			}
			else
			{
				$data = $gdt->var($gdt->getVar())->getGDOData();
				$back = array_merge($back, $data);
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
