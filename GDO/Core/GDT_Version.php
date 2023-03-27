<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * GDT_Version field.
 *
 * The $var is "Major.Minor.Patch".
 * The $value is a \GDO\Core\Version
 *
 * Validation via GDT_String::$pattern
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see Version
 * @see GDT_String
 */
class GDT_Version extends GDT_String
{

	public ?int $min = 5;
	public ?int $max = 16;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^\\d+\\.\\d+\\.\\d+$/iD";

	###################
	### Var / Value ###
	###################
	/**
	 * @param null|bool|int|float|string|object|array $value
	 */
	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value ? $value->__toString() : null;
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var ? new Version($var) : null;
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => Module_Core::GDO_VERSION],
		];
	}

	##############
	### Render ###
	##############
	public function renderCell(): string
	{
		$var = $this->getVar();
		return $var === null ? GDT::EMPTY_STRING : $var;
	}

}
