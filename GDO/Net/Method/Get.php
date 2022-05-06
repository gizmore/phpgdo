<?php
namespace GDO\Net\Method;

use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Form;
use GDO\Net\GDT_Url;
use GDO\Core\GDT_Response;
use GDO\Net\HTTP;

/**
 * Download website from the internet.
 * @author gizmore
 */
final class Get extends MethodCLI
{
	public function createForm(GDT_Form $form)
	{
		$form->addFields([
			GDT_Url::make('url'),
		]);
	}
	
	public function formValidated(GDT_Form $form)
	{
		HTTP::getFromURL($url);
		return new GDT_Response::m;
	}
	
}
