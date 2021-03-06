<?php
namespace GDO\Form;

use GDO\Core\Method;
use GDO\Core\GDT;
use GDO\File\GDT_File;
use GDO\Util\Common;

/**
 * A method with a form.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.2
 */
abstract class MethodForm extends Method
{
	private GDT_Form $form;
	
	public abstract function createForm(GDT_Form $form) : void;

	public function getFormName() : string
	{
		return 'form';
	}
	
	public function formValidated(GDT_Form $form)
	{
		$this->message('msg_form_validated');
		return $this->renderPage();
	}
	
	public function resetForm() : void
	{
		unset($this->form);
	}
	
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
			if (isset($this->inputs))
			{
				$this->addInputs($this->inputs);
				$this->applyInput();
			}
			$form = $this->getForm();
			$this->addComposeParameters($form->getAllFields());
			$this->addComposeParameters($form->actions()->getAllFields());
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
			$this->form = GDT_Form::make($this->getFormName());
			if (isset($this->inputs))
			{
				$this->form->inputs($this->inputs);
				$this->form->actions()->inputs($this->inputs);
			}
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
	
	public function execute()
	{
		$form = $this->getForm();
		
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
			if ($gdt->hasInput() && $gdt->isWriteable())
			{
				if ($form->validate(null))
				{
					if ($gdt->onclick)
					{
						return $gdt->click();
					}
					else
					{
						return $this->formValidated($form);
					}
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
	
	public function renderPage() : GDT
	{
		return $this->getForm();
	}
	
}
