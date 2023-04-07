<?php
declare(strict_types=1);
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Text;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Server-Side Base64 Decoding.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class DeBase64 extends MethodForm
{

	public function getCLITrigger(): string
	{
		return 'de64';
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Text::make('base64')->notNull(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		return GDT_Text::make('result')->var(base64_decode($this->gdoParameterVar('base64')));
	}

}
