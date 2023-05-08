<?php
namespace GDO\Net\Method;

use GDO\Core\GDT_String;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Net\GDT_Url;
use GDO\Net\HTTP;

/**
 * Download website from the internet.
 *
 * @author gizmore
 */
final class Get extends MethodForm
{

	public function getCLITrigger(): string
	{
		return 'wget';
	}

	protected function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Url::make('url')->notNull()->allowExternal(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onExecute']));
	}

	public function onExecute(): GDT_String
	{
		$url = $this->gdoParameterVar('url');
		$response = HTTP::getFromURL($url);
		return GDT_String::make()->var($response);
	}

}
