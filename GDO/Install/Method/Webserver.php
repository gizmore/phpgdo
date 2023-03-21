<?php
namespace GDO\Install\Method;

use GDO\Core\GDT_Template;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;

/**
 * Generate configurations for apache and nginx.
 *
 * @since 7.0.2
 * @author gizmore
 */
final class Webserver extends MethodForm
{

	public function createForm(GDT_Form $form): void
	{
		$form->text('install_info_webservers');
		$form->actions()->addFields(
			GDT_Submit::make('a22')->labelRaw('Apache 2.2')->onclick([$this, 'apache22']),
			GDT_Submit::make('a24')->labelRaw('Apache 2.4')->onclick([$this, 'apache24']),
			GDT_Submit::make('ngx')->labelRaw('nginx')->onclick([$this, 'nginx']),
			GDT_Submit::make('iis')->labelRaw('IIS')->onclick([$this, 'IIS']),
		);
	}

	public function apache22(): GDT_Template
	{
		return $this->templatePHP('httpd/apache22.php');
	}

	public function apache24(): GDT_Template
	{
		$tVars = [];
		return $this->templatePHP('httpd/apache24.php');
	}

	public function nginx(): GDT_Template
	{
		$tVars = [];
		return $this->templatePHP('httpd/nginx.php');
	}

	public function IIS(): GDT_Template
	{
		return $this->templatePHP('httpd/IIS.php');
	}

}
