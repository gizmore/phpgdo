<?php
namespace GDO\Net\Method;

use GDO\Form\GDT_Form;
use GDO\Net\GDT_Url;
use GDO\Net\HTTP;
use GDO\Form\MethodForm;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_String;

/**
 * Download website from the internet.
 * 
 * @author gizmore
 */
final class Get extends MethodForm
{
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(
			GDT_Url::make('url')->notNull()->reachable()->allowExternal(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make()->onclick([$this, 'onExecute']));
	}
	
	public function onExecute()
	{
		$url = $this->gdoParameterVar('url');
		$response = HTTP::getFromURL($url);
		return GDT_String::make()->var($response);
	}
	
}
