<?php
namespace GDO\Crypto;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_UInt;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Panel;

/**
 * Cryptographic functionality.
 * 
 * @author gizmore
 * @version 7.0.0
 * @see GDT_Password
 * @see GDT_PasswordHash
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
			GDT_Checkbox::make('password_strong')->initial('0'), // @TODO implement forcing strong passwords. GDO currently only needs 4 chars.
		];
	}
	public function cfgBCryptCost() : int { return $this->getConfigValue('bcrypt_cost'); }
	public function cfgPasswordStrong() : bool { return $this->getConfigValue('password_strong'); }
	
	public function getPrivacyRelatedFields(): array
	{
		return [
			GDT_Panel::make('info_crypto_hash_algo'),
		];
	}
}
