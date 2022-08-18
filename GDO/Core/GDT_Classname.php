<?php
namespace GDO\Core;

/**
 * A classname.
 * 
 * @author gizmore
 * @version 6.11.3
 * @since 6.11.3
 */
class GDT_Classname extends GDT_String
{
	public function defaultLabel() : self { return $this->label('classname'); }

	const LENGTH = 255;
	
	public int $min = 2;
	public int $max = self::LENGTH;
	
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/^[A-Za-z][A-Za-z _0-9\\\\]{1,254}$/sD";
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => GDT_Name::class],
		];
	}
	
	
}
