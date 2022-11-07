<?php
namespace GDO\CLI\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_Text;

/**
 * Server-Side Base64 Decoding.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class DeBase64 extends MethodForm
{
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Text::make('base64')->notNull(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		return GDT_Text::make('result')->var(base64_decode($this->gdoParameterVar('base64')));
	}
	
}
