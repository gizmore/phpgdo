<?php
namespace GDO\CLI\Method;

use GDO\CLI\CLI;
use GDO\CLI\MethodCLI;
use GDO\Core\GDT;
use GDO\Core\GDT_MethodSelect;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;

/**
 * Display help for a method.
 *
 * @author gizmore
 */
final class Help extends MethodCLI
{

	public function getCLITrigger(): string { return 'help'; }

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

	public function formValidated(GDT_Form $form): GDT
	{
		$method = $this->getParameterMethod();
		$help = CLI::renderCLIHelp($method);
		return GDT_String::make()->var($help);
	}

	private function getParameterMethod(): Method
	{
		return $this->gdoParameterValue('method');
	}

}
