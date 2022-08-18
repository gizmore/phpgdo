<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * Default random token is 16 chars alphanumeric.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 4.0.0
 */
class GDT_Token extends GDT_Char
{
	public function getDefaultName() : string { return 'token'; }
	public function defaultLabel() : self { return $this->label('token'); }
	
	protected function __construct()
	{
	    parent::__construct();
	    $this->length(GDO::TOKEN_LENGTH);
	}
	
	public function length($size) : self
	{
		$this->pattern = '/^[a-zA-Z0-9]{'.$size.'}$/D';
		return parent::length($size);
	}
	
	public bool $initialNull = false;
	public function initialNull(bool $initialNull=true) : self
	{
		$this->initialNull = $initialNull;
		return $this;
	}
	
	public function blankData() : array
	{
		return [
		    $this->name => $this->initialNull ? 
		        null : Random::randomKey($this->max)];
	}

	######################
	### Static helpers ###
	######################
	/**
	 * Generate a fixed compute token for arbritrary data.
	 * @TODO: Test crypto
	 */
	public static function generateToken(string $data, int $len=GDO::TOKEN_LENGTH) : string
	{
		$hash = sha1( md5($data) . md5(GDO_SALT) ); # the eye of the tiger
		return substr($hash, 0, $len);
	}
	
	public static function validateToken(string $token, string $data, int $len=GDO::TOKEN_LENGTH) : bool
	{
		$compare = self::generateToken($data, $len);
		return $token === $compare;
	}
	
	public function plugVars() : array
	{
		if ($this->initialNull)
		{
			return [[$this->getName() => null]];
		}
		return [
			[$this->getName() => Random::mrandomKey($this->max)],
		];
	}
	
}
