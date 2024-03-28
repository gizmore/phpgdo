<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Util\Regex;
use GDO\Util\Strings;

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
        $this->parts = $this->parseURL($url);
	}

	public function getScheme(): ?string
	{
        if (isset($this->parts['scheme']))
        {
            return $this->parts['scheme'];
        }
        else
        {
            return Strings::substrTo($this->raw, '://', self::localScheme());
        }
	}

	public static function localScheme(): string
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

    private function parseURL($url)
    {
        if (str_starts_with($url, '/'))
        {
            if (str_starts_with($url, '//')) # use current https/http scheme
            {
                $url = GDO_PROTOCOL . ':' . $url;
            }
        }

        $matches = [];
        if (preg_match('/^([a-z]{3,8}):\\/\\/([-.a-z]+):?([0-9]+)?\\/?([^?#]+)?(\\?[^#]+)?/i', $url, $matches))
        {
            $parts = [
                'scheme' => $matches[1],
                'host' => $matches[2],
                'port' => (int) @$matches[3],
                'path' => @$matches[4],
                'query' => @$matches[5],
            ];
            return $parts;
        }
        else
        {
            return GDT::EMPTY_ARRAY;
        }
    }

}
