<?php
namespace GDO\Crypto;

use GDO\Core\GDO_Module;

/**
 * Cryptographic functionality.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Module_Crypto extends GDO_Module
{
	public int $priority = 10;
	
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/crypto');
	}
	
}
