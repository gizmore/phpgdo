<?php
declare(strict_types=1);
namespace GDO\Net;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\UI\WithAnchorRelation;
use GDO\UI\WithTitle;
use GDO\Util\Arrays;

/**
 * URL field.
 * Features link checking.
 *
 * @version 7.0.3
 *
 * @since 5.0.0
 * @see URL.
 *
 * @author gizmore
 */
class GDT_Url extends GDT_String
{

	use WithTitle;
	use WithAnchorRelation;

	public bool $noFollow = false;
	public bool $reachable = false;
	public bool $allowInternal = false;

	##############
	### Static ###
	##############
	public bool $allowExternal = false;
	public array $schemes = ['http', 'https'];

	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'url';
		$this->ascii()->caseS();
		$this->min(1)->max(767);
	}

	public static function absolute($url, bool $forceSSL = false): string
	{
		$protocol = $forceSSL ? 'https' : self::protocol();
		return sprintf('%s://%s%s', $protocol, self::hostWithPort($protocol), $url);
	}

	public static function protocol(): string { return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }

	public static function hostWithPort(string $protocol = GDO_PROTOCOL): string
	{
		$port = self::port();
		if ($protocol === 'https')
		{
			$port = $port === 80 ? 443 : $port;
			$port = $port == 443 ? GDT::EMPTY_STRING : ":{$port}";
		}
		else
		{
			$port = $port === 443 ? 80 : $port;
			$port = $port == 80 ? GDT::EMPTY_STRING : ":{$port}";
		}
		return self::host() . $port;
	}

	###############
	### Options ###
	###############

	public static function port(): ?int { return def('GDO_PORT', (int)(@$_SERVER['SERVER_PORT']?$_SERVER['SERVER_PORT']:443)); }

	public static function host(): string { return def('GDO_DOMAIN', @$_SERVER['SERVER_NAME']?$_SERVER['SERVER_NAME']:GDO_DOMAIN); }

	public static function relative($url): string { return GDO_WEB_ROOT . $url; }

	public function getInputType(): string
	{
		return 'url';
	}

	public function gdtDefaultLabel(): ?string
    {
        return 'url';
    }

	###################
	### Var / Value ###
	###################

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var ? new URL($var) : null;
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value ? $value->raw : null;
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return GDT_Template::php('Net', 'url_html.php', ['field' => $this]);
	}

    public function renderCell(): string
    {
        return $this->renderHTML();
    }

    ###############
	### Options ###
	###############

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value ? $value->raw : null))
		{
			return false;
		}
		return $this->validateUrl($value);
	}

	public function validateUrl(URL $url = null): bool
	{
		# null allowed by parent validator
		if ((!$url) || (null == ($value = $url->raw)))
		{
			return true;
		}

		# Check local
		if (!$this->allowInternal)
		{
			# Check relative url
			if ($value[0] === '/')
			{
				return $this->errorLocal($value);
			}

			# Check by IP
            if ($host = $url->getHost())
            {
                $ip = gethostbyname($host);
            }
            else
            {
                return $this->errorNull();
            }
			if (GDT_IP::isLocal($ip))
			{
				return $this->errorLocal($value);
			}
			if ($ip === @$_SERVER['SERVER_ADDR'])
			{
				return $this->errorLocal($value);
			}
		}

		if ((!$this->allowExternal) && ($value[0] !== '/'))
		{
			return $this->error('err_external_url_not_allowed', [html($value)]);
		}

		# Check schemes (if external). internal are always prefixed with /
		if ($this->allowExternal)
		{
			if (isset($this->schemes) && count($this->schemes))
			{
                $scheme = $url->getScheme();
				if (!in_array($scheme, $this->schemes, true))
				{
					return $this->error('err_url_scheme', [Arrays::implodeHuman($this->schemes)]);
				}
			}
		}

		# Check reachable
		if ($this->reachable)
		{
			if ($value[0] === '/')
			{
				return true; # bailout early
			}
			if (!HTTP::pageExists($value))
			{
				return $this->error('err_url_not_reachable', [html($value)]);
			}
		}

		return true;
	}

	private function errorLocal($value): bool
	{
		return $this->error('err_local_url_not_allowed');
	}

	public function plugVars(): array
	{
		$plugs = [];
		if ($this->allowInternal)
		{
			$plugs[] = [$this->getName() => hrefDefault()];
		}
		if ($this->allowExternal)
		{
			$plugs[] = [$this->getName() => 'https://www.wechall.net'];
		}
		return $plugs;
	}

	public function allowAll(bool $reachable = true): static
	{
		$this->allowInternal = true;
		$this->allowExternal = true;
		return $this->reachable($reachable);
	}

	public function reachable(bool $reachable = true): static
	{
		$this->reachable = $reachable;
		return $this;
	}

	################
	### Validate ###
	################

	public function allowInternal(bool $allowInternal = true): static
	{
		$this->allowInternal = $allowInternal;
		return $this;
	}

	public function allowExternal(bool $allowExternal = true, bool $reachable = true): static
	{
		$this->allowExternal = $allowExternal;
		return $this->reachable($reachable);
	}

	public function schemes(string...$schemes): static
	{
		$this->schemes = $schemes;
		return $this;
	}

	#############
	### Test ###
	#############

	public function allSchemes(): static
	{
		unset($this->schemes);
		return $this;
	}


    public function getURL(): URL
    {
        return $this->getValue();
    }

    public function getAbsoluteURL(): URL
    {
        return $this->getURL();
    }

}
