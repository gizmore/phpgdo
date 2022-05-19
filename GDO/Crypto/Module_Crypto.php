<?php
namespace GDO\Crypto;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;

/**
 * Cryptographic functionality.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Module_Crypto extends GDO_Module
{
	public int $priority = 3;
	
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/crypto');
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_UInt::make('bcrypt_cost')->min(1)->max(32)->initial('11'),
		];
	}
	public function cfgBCryptCost() : int { return $this->getConfigValue('bcrypt_cost'); }
	
}
