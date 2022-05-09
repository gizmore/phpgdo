<?php
namespace GDO\Form;

use GDO\Core\Method;

abstract class MethodForm extends Method
{
	public abstract function createForm(GDT_Form $form) : void;
	
	public function execute() : GDT
	{
		
	}
	
}
