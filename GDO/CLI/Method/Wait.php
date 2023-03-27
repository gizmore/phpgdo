<?php
namespace GDO\CLI\Method;

use GDO\CLI\MethodCLI;
use GDO\Core\GDT;
use GDO\Date\GDT_Duration;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\UI\GDT_Success;

/**
 * Wait a specified duration.
 *
 * @version 6.11.5
 * @author gizmore
 */
final class Wait extends MethodCLI
{

	public function getCLITrigger(): string { return 'wait'; }

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Duration::make('duration')->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onExecute']));
	}

	public function execute(): GDT
	{
		$seconds = $this->gdoParameterValue('duration');
		usleep($seconds * 1000000);
		return GDT_Success::make();
	}

}
