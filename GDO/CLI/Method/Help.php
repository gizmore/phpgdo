<?php
namespace GDO\CLI\Method;

use GDO\CLI\CLI;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Form\GDT_Form;
use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_MethodSelect;
use GDO\Form\GDT_AntiCSRF;

/**
 * Display help for a method.
 * 
 * @author gizmore
 */
final class Help extends MethodCLI
{
	public function getCLITrigger() { return 'help'; }
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_MethodSelect::make('method')->notNull()->onlyPermitted(false),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(
			GDT_Submit::make(),
		);
	}

	private function getParameterMethod(): Method
	{
		return $this->gdoParameterValue('method');
	}
	
	public function formValidated(GDT_Form $form)
	{
		$method = $this->getParameterMethod();
		$help = CLI::renderCLIHelp($method);
		return GDT_String::make()->var($help);
	}
	
}
