<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT_String;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Convert a string to uppercase.
 * Mostly a test method, but might be useful.
 *
 * @version 7.0.0
 * @since 7.0.0
 * @author gizmore
 * @see Concat
 */
final class Uppercase extends MethodForm
{

	public function getCLITrigger(): string
	{
		return 'upper';
	}

	protected function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_String::make('string')->notNull(),
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
