<?php
declare(strict_types=1);
namespace GDO\Form;

use GDO\Core\GDO_ArgException;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\File\GDT_File;

/**
 * A method with a form.
 *
 * @version 7.0.3
 * @since 5.0.2
 * @author gizmore
 */
abstract class MethodForm extends Method
{

	public bool $submitted = false;

	public ?string $pressedButton = null;

	#################
	### Submitted ###
	#################
	public bool $validated = false;
	protected GDT_Form $form;

	public function isUserRequired(): bool
	{
		return true;
	}

	#################
	### Validated ###
	#################

	public function isLocking(): bool
	{
		return true;
	}

	public function submitted(bool $submitted = true): self
	{
		$this->submitted = $submitted;
		return $this;
	}

	############
	### Form ###
	############

	public function validated(bool $validated = true): self
	{
		$this->validated = $validated;
		return $this;
	}

	/**
	 * Reset a form.
	 * Clear the form.
	 * Reset GDT to initial.
	 * Optionally remove inputs.
	 */
	public function resetForm(bool $removeInput = false): void
	{
		if (isset($this->form))
		{
			$fields = $this->form->getAllFields();
			foreach ($fields as $gdt)
			{
				$gdt->reset();
			}
			unset($this->form);
		}
		if ($removeInput)
		{
			unset($this->inputs);
		}
		unset($this->parameterCache); # :)
	}


	public function getForm(bool $reset = false): GDT_Form
	{
		if ($reset)
		{
			unset($this->form);
// 			unset($this->parameterCache);
		}
		if (!isset($this->form))
		{
			$inputs = $this->getInputs();
			$this->submitted = false;
			$this->validated = false;
			$this->pressedButton = null;
			$this->form = GDT_Form::make($this->getFormName());
			$this->createForm($this->form);
			$this->form->inputs($inputs);
			$this->addComposeParameters($this->form->getAllFields());
			$this->addComposeParameters($this->form->actions()->getAllFields());
			$this->applyInput();
//			$this->form->actions()->inputs($inputs);
// 			$this->form->titleRaw($this->getMethodTitle());
		}
		return $this->form;
	}

	public function getFormName(): string
	{
		return 'form';
	}

	abstract public function createForm(GDT_Form $form): void;

	protected function applyInput(): void
	{
		parent::applyInput();
		$this->getForm();
	}

	/**
	 * @throws GDO_ArgException
	 */
	public function execute(): GDT
	{
		### validation result
		$this->submitted = false;
		$this->validated = false;
		$this->pressedButton = null;

		### Generate form
		$form = $this->getForm(true);

		$this->appliedInputs($this->getInputs());

		$form->titleRaw($this->getMethodTitle());

		### Flow upload
		if ($flowField = ($this->inputs['flowField'] ?? null))
		{
			/** @var GDT_File $formField * */
			if ($formField = $form->getField($flowField))
			{
				return $formField->flowUpload();
			}
		}

		### Execute action
		foreach ($form->actions()->getAllFields() as $gdt)
		{
			/** @var GDT_Submit $gdt * */
			$gdt->inputs($this->getInputs());
			if ($gdt->hasInput() && $gdt->isWriteable())
			{
				$this->submitted = true;
				$this->pressedButton = $gdt->getName();

				$this->beforeValidation();

				if ($form->validate(null))
				{
					$this->validated = true;

					# submit events
					$this->onSubmitted();

					#PP#begin#
					if ($this->isDebugging())
					{
						xdebug_break();
					}
					#PP#end#

					# Click it
					if ($gdt->onclick)
					{
						$result = $gdt->click($form);
					}
					elseif ($gdt->name === 'submit')
					{
						$result = $this->formValidated($form);
					}
					else
					{
						return GDT_Response::make();
					}

					$this->afterValidation();

					return $result;
				}
				else
				{
					$form->errorFormInvalid();
					return $this->formInvalid($form);
				}
			}
		}
		return $this->renderPage();
	}

	/**
	 * Get the parameter cache.
	 *
	 * @return GDT[]
	 */
	public function &gdoParameterCache(): array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
			$form = $this->getForm(true);
			$this->addComposeParameters($form->getAllFields());
			$this->addComposeParameters($form->actions()->getAllFields());
// 			$this->applyInput();
		}
		return $this->parameterCache;
	}

// 	public function executeEditMethods()
// 	{
// 		if (count($_POST))
// 		{
// 			foreach ($this->getForm()->getAllFields() as $field)
// 			{
// 				if ($field instanceof GDT_File)
// 				{
// 					$key = 'delete_' . $field->name;
// 					if (isset($_REQUEST[$this->formName()][$key]))
// 					{
// 						$fileIds = array_keys($_REQUEST[$this->formName()][$key]);
// 						$field->onDeleteFiles($fileIds);
// 					}
// 				}
// 			}
// 		}
// 	}

	############
	### Init ###
	############

	public function getMethodTitle(): string
	{
		$key = sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return t($key);
	}

	protected function beforeValidation(): void {}

	############
	### Exec ###
	############

	private function onSubmitted(): void
	{
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->onSubmitted();
		}
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$this->message('msg_form_validated');
		return $this->renderPage();
	}

	/**
	 * @TODO renderPage() is not a GDT method. Maybe rename and make it a Method thingy.
	 */
	public function renderPage(): GDT
	{
		$form = $this->getForm();
		$form->titleRaw($this->getMethodTitle());
		return $form;
	}

//	protected function renderMoMe(): string
//	{
//		$mo = $this->getModuleName();
//		$me = $this->getMethodName();
//		return "{$mo}::{$me}";
//	}

	protected function afterValidation(): void {}

	### Override ###

	public function formInvalid(GDT_Form $form): GDT
	{
		return $this->renderPage();
	}

}
