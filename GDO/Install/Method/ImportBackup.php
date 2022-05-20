<?php
namespace GDO\Install\Method;

use GDO\Core\GDT_Template;
use GDO\File\GDT_File;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Import a backup.
 * Requires Module_Backup.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.12.3
 */
final class ImportBackup extends MethodForm
{
	public function renderPage()
	{
		return GDT_Template::responsePHP('Install', 'page/importbackup.php', ['form' => $this->getForm()]);
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_File::make('backup_file'),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onImportBackup']));
	}
	
	public function onImportBackup()
	{
		$form = $this->getForm();
		if (module_enabled('Backup'))
		{
			return \GDO\Backup\Method\ImportBackup::make()->importBackup($form->getFormValue('backup_file'));
		}
		
		return parent::formValidated($form)->addField($this->renderPage());
	}
	
}
