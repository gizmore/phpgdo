<?php
namespace GDO\Core;

use GDO\Util\Random;

/**
 * A GDT_Char is a fixed length CHAR value in the db.
 *
 * @version 7.0.0
 * @since 5.0.0
 * @author gizmore
 */
class GDT_Char extends GDT_String
{

	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;

	public function length($size)
	{
		$this->min = $this->max = $size;
		return $this;
	}

	public function plugVars(): array
	{
		return [
			[$this->name => Random::mrandomKey($this->min, Random::HEXUPPER)],
		];
	}

}
