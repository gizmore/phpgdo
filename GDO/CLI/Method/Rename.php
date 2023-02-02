<?php
namespace GDO\CLI\Method;

use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Form;
use GDO\Core\GDT_Path;
use GDO\Form\GDT_Submit;

/**
 * Mass rename utility.
 * 
 * @author gizmore
 * @since 7.0.2
 */
final class Rename extends MethodCLI
{
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Path::make('path')->existingDir()->notNull(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
}
