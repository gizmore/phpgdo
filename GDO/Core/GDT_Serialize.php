<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Datatype that uses PHP serialize to store arbitrary data.
 * Used in Session.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 * @see GDO_Session
 */
class GDT_Serialize extends GDT_Text
{

	public ?int $max = 65535;
	public int $encoding = self::BINARY;
	public bool $writeable = false;
	public bool $caseSensitive = true;

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value === null ? null : self::serialize($value);
	}

	public static function serialize($data): string
	{
		return serialize($data);
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : self::unserialize($var);
	}

	public static function unserialize(string $string)
	{
		return unserialize($string);
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => self::serialize(['a' => '1'])],
		];
	}

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!(parent::validate($this->getVar())))
		{
			return false;
		}
		return true;
	}

}
