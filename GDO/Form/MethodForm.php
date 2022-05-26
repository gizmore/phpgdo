<?php
namespace GDO\Form;

use GDO\Core\Method;
use GDO\Core\GDT;
use GDO\Core\GDT_Tuple;
use GDO\UI\GDT_Success;

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

	public function formValidated(GDT_Form $form)
	{
		return GDT_Tuple::makeWith(
			GDT_Success::make()->text('msg_form_validated'),
			$this->renderPage());
	}
	
// 	publiC FUNCTION INPUTS(ARRAY $INPUTS) : SELF
// 	{
// 		$THIS->FORM->INPUTS($INPUTS);
// 		RETURN $THIS;
// 	}
	
	public function gdoComposeParameters() : array
	{
		$form = $this->getForm();
		return array_merge(
			$this->gdoParameters(),
			$form->getAllFields(),
			$form->actions()->getAllFields());
	}
	
	public function getForm() : GDT_Form
	{
		if (!isset($this->form))
		{
			$this->form = GDT_Form::make('form');
			if (isset($this->inputs))
			{
				$this->form->inputs($this->inputs);
				$this->form->actions()->inputs($this->inputs);
			}
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
	public function execute()
	{
		$form = $this->getForm();
		foreach ($form->actions()->getAllFields() as $gdt)
		{
			if ($gdt->hasInput())
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
					return $form;
				}
			}
		}
		return $this->renderPage();
	}
	
	public function renderPage() : GDT
	{
		return $this->getForm();
	}
	
	public function withAppliedInputs(array $inputs) : self
	{
		$this->getForm()->actions()->inputs($inputs);
		return parent::withAppliedInputs($inputs);
	}

}
