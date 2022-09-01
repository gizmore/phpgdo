<?php
namespace GDO\Crypto;

use GDO\Core\GDT_Char;

/**
 * Very simple md5.
 * 
 * @author gizmore
 */
final class GDT_MD5 extends GDT_Char
{
	protected function __construct()
	{
		parent::__construct();
		$this->length(32);
		$this->caseS();
		$this->ascii();
		$this->pattern('/^[a-z0-9]{32}$/iD');
	}
	
}
