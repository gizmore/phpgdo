<?php
namespace GDO\Core;

/**
 * A classname.
 *
 * @version 6.11.3
 * @since 6.11.3
 * @author gizmore
 */
class GDT_Classname extends GDT_String
{

	public const LENGTH = 255;
	public int $min = 2;
	public int $max = self::LENGTH;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[A-Za-z][A-Za-z _0-9\\\\]{1,254}$/sD";

	public function defaultLabel(): self { return $this->label('classname'); }

	public function plugVars(): array
	{
		return [
			[$this->getName() => GDT_Name::class],
		];
	}


}
