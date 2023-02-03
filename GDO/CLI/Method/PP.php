<?php
namespace GDO\CLI\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Core\GDT_Text;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_AntiCSRF;
use GDO\Core\GDT_String;

/**
 * PP - The PHP Preprocessor
 * Public API for the lulz.
 * 
 * For syntax see the official repository.
 * 
 * @link https://github.com/gizmore/php-preprocessor
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
final class PP extends MethodForm
{
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Text::make('text')->notNull(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function formValidated(GDT_Form $form)
	{
		$string = $form->getFormVar('text');
		$string = \GDO\Util\PP::init()->processString($string);
		return GDT_String::make()->var($string);
	}
	
}
