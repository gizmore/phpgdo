<?php
namespace GDO\Install\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Import a backup.
 * Requires Module_Backup.
 *
 * @version 7.0.0
 * @since 6.12.3
 * @author gizmore
 */
final class ImportBackup extends MethodForm
{

	public function isUserRequired(): bool
	{
		return false;
	}

	public function onImportBackup()
	{
		$form = $this->getForm();
		if (module_enabled('Backup'))
		{
			$backup = method('Backup', 'ImportBackup');
			return $backup->importBackup($form->getFormValue('backup_file'));
		}

		return parent::formValidated($form)->addField($this->renderPage());
	}

	public function renderPage(): GDT
	{
		return GDT_Template::make()->template('Install', 'page/importbackup.php', ['form' => $this->getForm()]);
	}

	public function getMethodDescription(): string
	{
		return $this->getMethodTitle();
	}

	public function getMethodTitle(): string
	{
		return t('install_title_8');
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_String::make('backup_file'),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onImportBackup']));
	}


}
