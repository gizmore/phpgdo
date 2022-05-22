<?php
namespace GDO\Form;

use GDO\Core\GDT;
use GDO\Core\Method;

/**
 * A method with a form.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.2
 */
abstract class MethodForm extends Method
{
	private GDT_Form $form;
	
	public abstract function createForm(GDT_Form $form) : void;
	
	public function getForm() : GDT_Form
	{
		if (!isset($this->form))
		{
			$this->form = GDT_Form::make('form');
			$this->createForm($this->form);
		}
		return $this->form;
	}
	
	public function execute()
	{
		$form = $this->getForm();
		$form->actions()->withFields(function(GDT $gdt){
			if ($gdt->hasInput())
			{
				return $gdt->click();
			}
		});
		
		return $form;
	}
	
}
