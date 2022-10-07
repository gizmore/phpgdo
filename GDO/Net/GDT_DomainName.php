<?php
namespace GDO\Net;

use GDO\Core\GDT_String;

final class GDT_DomainName extends GDT_String
{
	public string $pattern = "/[\\.a-z]+\\.[a-z]+$/iD";
	
	public $tldonly = false;
	public function tldonly($tldonly=true)
	{
		$this->tldonly = $tldonly;
		return $this;
	}
	
	public function validate($value) : bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		if ($value === null)
		{
			return true;
		}
		
		$parts = explode('.', $value);
		if ($this->tldonly && count($parts) !== 2)
		{
			return $this->error('err_domain_no_tld');
		}
		
		return true;
	}
	
	public function plugVars(): array
	{
		if ($this->tldonly)
		{
			return [
				[$this->getName() => 'wechall.net'],
			];
		}
		return [
			[$this->getName() => 'www.wechall.net'],
		];
	}

}
