<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * A classname.
 *
 * @version 7.0.3
 * @since 6.11.3
 * @author gizmore
 */
class GDT_Classname extends GDT_String
{

	final public const LENGTH = 255;
	public ?int $min = 2;
	public ?int $max = self::LENGTH;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[A-Z][A-Z _0-9\\\\]{1,254}$/siD";

	public function gdtDefaultLabel(): ?string
	{
		return 'classname';
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => GDT_Name::class],
		];
	}


}
