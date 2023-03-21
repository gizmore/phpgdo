<?php
namespace GDO\Net;

use GDO\Core\GDT_String;

/**
 * @author gizmore
 */
final class GDT_DomainName extends GDT_String
{

	public string $pattern = "/[\\.a-z]+\\.[a-z]+$/iD";

	public bool $tldOnly = false;

	public function tldOnly(bool $tldOnly = true): self
	{
		$this->tldOnly = $tldOnly;
		return $this;
	}

	public function validate($value): bool
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
		if ($this->tldOnly && count($parts) !== 2)
		{
			return $this->error('err_domain_no_tld');
		}

		return true;
	}

	public function plugVars(): array
	{
		if ($this->tldOnly)
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
