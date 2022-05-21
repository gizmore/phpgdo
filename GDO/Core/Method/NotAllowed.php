<?php
namespace GDO\Core\Method;

use GDO\Core\Module_Core;
use GDO\UI\MethodPage;

/**
 * Send an email if opted-in.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class NotAllowed extends MethodPage
{
	public function beforeExecute() : void
	{
		if (Module_Core::instance()->cfgMail403())
		{
			$this->send403Mail();
		}
	}
	
	public function send403Mail() : void
	{
		
	}
	
	public function execute()
	{
		return $this->pageTemplate('403_page');
	}
	
}
