<?php
namespace GDO\Core;

/**
 * Version field. It's value is a Version.
 * The $var is "Major.Minor.Patch". 
 * 
 * @see Version
 * @see GDT_String
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
final class GDT_Version extends GDT_String
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
	public function toVar($value) : string
	{
		return sprintf('%d.%d.%d', $value->major, $value->minor, $value->patch);
	}
	
	public function toValue(string $var)
	{
		return new Version($var);
	}
	
}
