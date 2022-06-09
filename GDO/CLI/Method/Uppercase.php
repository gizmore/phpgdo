<?php
namespace GDO\CLI\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_String;
use GDO\Form\GDT_AntiCSRF;

/**
 * Convert a string to uppercase.
 * Mostly a test method, but might be useful.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see Concat
 */
final class Uppercase extends MethodForm
{
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_String::make('string')->required(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(
			GDT_Submit::make()->
			onclick([$this, 'uppercase']));
	}
	
	public function uppercase()
	{
		$s = $this->gdoParameterVar('string');
		$s = mb_strtoupper($s, 'UTF-8');
		return GDT_String::make()->var($s);
	}

}
