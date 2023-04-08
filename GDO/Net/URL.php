<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\Application;
use GDO\Util\Regex;

/**
 * This class holds url parts and the raw url.
 * It is the return value of GDT_Url->toValue().
 *
 * @version 7.0.3
 * @sinve 6.0.2
 *
 * @author gizmore
 * @see GDT_Url
 */
final class URL
{

	##############
	### Static ###
	##############
	public string $raw;

	###############
	### Members ###
	###############
	public array $parts;

	public function __construct($url)
	{
		$this->raw = $url;
		$this->parts = parse_url($url);
	}

	public function getScheme(): ?string
	{
		return $this->parts['scheme'] ?? self::localScheme();
	}

	public static function localScheme() :string
	{
		if (Application::$INSTANCE->isCLI())
		{
			return GDO_PROTOCOL;
		}
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
	}

	public function getPort(): int
	{
		return $this->parts['port'];
	}

	public function getTLD(): ?string
	{
		return Regex::firstMatch('/([^.]+\\.[^.]+)$/ui', $this->getHost());
	}

	public function getHost(): ?string
	{
		if (isset($this->parts['host']))
		{
			return $this->parts['host'];
		}
		if (isset($this->parts['path']))
		{
			return $this->parts['path'];
		}
		return null;
	}

}
