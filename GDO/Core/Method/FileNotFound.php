<?php
namespace GDO\Core\Method;

use GDO\Net\GDT_Url;
use GDO\UI\MethodPage;
use GDO\Core\Module_Core;

final class FileNotFound extends MethodPage
{
	public function gdoParameters() : array
	{
		return [
			GDT_Url::make('url')->notNull()->allowInternal(),
		];
	}
	
	public function beforeExecute() : void
	{
		if (Module_Core::instance()->cfgMail404())
		{
			$this->send404Mail();
		}
	}
	
	public function send404Mail() : void
	{
		
	}
	
	public function execute()
	{
		return $this->pageTemplate('404_page');
	}
	
}
