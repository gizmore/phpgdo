<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Util\Strings;

/**
 * Version number. Major.Minor.Patch.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see GDO_Module
 */
final class Version
{

	final public const MAX_MINOR = 9;
	final public const MAX_PATCH = 9;

	public int $major = 0;
	public int $minor = 0;
	public int $patch = 0;

	public function __construct(string $var = null)
	{
		if ($var)
		{
			[$major, $minor, $patch] = explode('.', Strings::substrTo($var, 'r', $var));
			$this->major = intval($major);
			$this->minor = intval($minor);
			$this->patch = intval($patch);
		}
	}

	public function __toString(): string
	{
		return sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);
	}

	/**
	 * Increase the version by 1 patch level.
	 */
	public function increase(): self
	{
		$this->patch++;
		if ($this->patch > self::MAX_PATCH)
		{
			$this->patch = 0;
			$this->minor++;
			if ($this->minor > self::MAX_MINOR)
			{
				$this->major++;
				$this->minor = 0;
			}
		}
		return $this;
	}

}
