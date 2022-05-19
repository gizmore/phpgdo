<?php
namespace GDO\CLI\Method;

use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Form;
use GDO\Date\GDT_Duration;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Submit;
use GDO\UI\GDT_Success;

/**
 * Wait a specified duration.
 * 
 * @author gizmore
 * @version 6.11.5
 */
final class Wait extends MethodCLI
{
	public function getCLITrigger() { return 'wait'; }
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_Duration::make('duration')->required(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onExecute']));
	}

	public function onExecute()
	{
		$form = $this->getForm();
		usleep($form->getFormValue('duration'));
		return GDT_Success::make();
	}

	
}
