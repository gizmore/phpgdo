<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * Default random token is 16 chars alphanumeric.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 4.0.0
 */
class GDT_Token extends GDT_Char
{
	public function defaultName() { return 'token'; }
	public function defaultLabel() { return $this->label('token'); }
	
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
	
}
