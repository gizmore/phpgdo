<?php
namespace GDO\Install\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\Form\GDT_Submit;

/**
 * Generate configurations for apache and nginx.
 * 
 * @author gizmore
 * @since 7.0.2
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
	
	public function apache22()
	{
		$tVars = [];
		return $this->templatePHP('httpd/apache22.php', $tVars);
	}
	
	public function apache24()
	{
		$tVars = [];
		return $this->templatePHP('httpd/apache24.php', $tVars);
	}
	
	public function nginx()
	{
		$tVars = [];
		return $this->templatePHP('httpd/nginx.php', $tVars);
	}
	
	public function IIS()
	{
		$tVars = [];
		return $this->templatePHP('httpd/IIS.php', $tVars);
	}
	
}
