<?php
namespace GDO\CLI\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_String;
use GDO\Form\GDT_AntiCSRF;

/**
 * @author gizmore
 */
final class Uppercase extends MethodForm
{
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_String::make('string')->required(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'uppercase']));
	}

	
}
