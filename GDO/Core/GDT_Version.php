<?php
namespace GDO\Core;

/**
 * Version field. It's value is a Version.
 * The $var is "Major.Minor.Patch". 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see Version
 * @see GDT_String
 */
class GDT_Version extends GDT_String
{
	public int $min = 5;
	public int $max = 14;
	public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	public string $pattern = "/\\d+\\.\\d+\\.\\d+/iD";
	
	/**
	 * @param Version $value
	 * @return string
	 */
	public function toVar($value) : ?string
	{
		if (!$value)
		{
			return null;
		}
		return sprintf('%d.%d.%d', $value->major, $value->minor, $value->patch);
	}
	
	public function toValue(string $var = null)
	{
		if (!$var)
		{
			return null;
		}
		return new Version($var);
	}
	
}
