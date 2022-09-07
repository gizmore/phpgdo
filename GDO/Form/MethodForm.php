<?php
namespace GDO\Form;

use GDO\Core\Method;
use GDO\Core\GDT;
use GDO\File\GDT_File;
use GDO\Util\Common;
use GDO\Core\GDO_Error;

/**
 * A method with a form.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.2
 */
abstract class MethodForm extends Method
{
	public function isLocking() : bool
	{
		return true;
	}
	
	#################
	### Submitted ###
	#################
	public bool $submitted = false;
	public ?string $pressedButton = null;
	public function submitted(bool $submitted=true) : self
	{
		$this->submitted = $submitted;
		return $this;
	}
	
	#################
	### Validated ###
	#################
	public bool $validated = false;
	public function validated(bool $validated=true) : self
	{
		$this->validated = $validated;
		return $this;
	}
	
	############
	### Form ###
	############
	protected GDT_Form $form;
	
	public function getFormName() : string
	{
		return 'form';
	}
	
	public abstract function createForm(GDT_Form $form) : void;

	public function formValidated(GDT_Form $form)
	{
		$this->message('msg_form_validated');
		return $this->renderPage();
	}

	/**
	 * Reset a form.
	 * Clear the form.
	 * Reset GDT to initial.
	 * Optionally remove inputs.
	 */
	public function resetForm(bool $removeInput = false) : void
	{
		if (isset($this->form))
		{
			$fields = $this->form->getAllFields();
			foreach ($fields as $gdt)
			{
				$gdt->reset($removeInput);
			}
			unset($this->form);
		}
		if ($removeInput)
		{
			unset($this->inputs);
		}
	}
	
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
			$this->applyInput();
			$form = $this->getForm();
			$this->addComposeParameters($form->getAllFields());
			$this->addComposeParameters($form->actions()->getAllFields());
			$this->applyInput();
		}
		return $this->parameterCache;
	}
	
	public function getMethodTitle() : string
	{
		$key = sprintf('mt_%s_%s', $this->getModuleName(), $this->getMethodName());
		$key = strtolower($key);
		return t($key);
	}
	
	public function getForm() : GDT_Form
	{
		if (!isset($this->form))
		{
			$inputs = $this->getInputs();
			$this->submitted = false;
			$this->validated = false;
			$this->pressedButton = null;
			$this->form = GDT_Form::make($this->getFormName());
			$this->form->inputs($inputs);
			$this->form->actions()->inputs($inputs);
			$this->form->titleRaw($this->getMethodTitle());
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
// 	public function executeEditMethods()
// 	{
// 		if (count($_POST))
// 		{
// 			foreach ($this->getForm()->getFieldsRec() as $field)
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
	### Exec ###
	############
	protected function beforeValidation() : void {}
	protected function afterValidation() : void {}

	public function execute()
	{
		### validation result 
		$this->submitted = false;
		$this->validated = false;
		$this->pressedButton = null;
		
		### Generate form
		$form = $this->getForm();
		
		if (isset($this->inputs))
		{
			$form->inputs($this->inputs);
		}
		
		### Flow upload
		if ($flowField = Common::getRequestString('flowField'))
		{
			/** @var $formField GDT_File **/
			if ($formField = $form->getField($flowField))
			{
				return $formField->flowUpload();
			}
		}

		### Execute action
		foreach ($form->actions()->getAllFields() as $gdt)
		{
			/** @var $gdt GDT_Submit **/
// 			$gdt->inputs($this->getInputs());
			if ($gdt->hasInput() && $gdt->isWriteable())
			{
				$this->submitted = true;
				$this->pressedButton = $gdt->name;
				
				$this->beforeValidation();
				
				if ($form->validate(null))
				{
					$this->validated = true;

					# submit events
					$this->onSubmitted();
					
					# Click it
					if ($gdt->onclick)
					{
						$result = $gdt->click($form);
					}
					elseif ($this->pressedButton === 'submit')
					{
						$result = $this->formValidated($form);
					}
					else
					{
						throw new GDO_Error('err_submit_without_click_handler', [
							$this->renderMoMe(), $gdt->getName()]);
					}

					$this->afterValidation();
					
					return $result;
				}
				else
				{
					$form->errorFormInvalid();
					return $this->renderPage();
				}
			}
		}
		return $this->renderPage();
	}
	
	protected function renderMoMe() : string
	{
		$mo = $this->getModuleName();
		$me = $this->getMethodName();
		return "{$mo}::{$me}";
	}
	
	private function onSubmitted() : void
	{
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->onSubmitted();
		}
	}

	### Override ###
	/**
	 * @TODO renderPage() is not a GDT method. Maybe rename and make it a Method thingy.
	 */
	public function renderPage() : GDT
	{
		return $this->getForm();
	}
	
}
