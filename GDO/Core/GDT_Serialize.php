<?php
namespace GDO\Core;

/**
 * Datatype that uses PHP serialize to store arbitrary data.
 * Used in Session.
 * 
 * @author gizmore
 * @see GDO_Session
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_Serialize extends GDT_Text
{
	public static function serialize($data)
	{
		return base64_encode(serialize($data));
	}
	
	public static function unserialize($string)
	{
		return unserialize(base64_decode($string));
	}

	public int $max = 65535;
	public int $encoding = self::BINARY;
	public bool $writeable = false;
	public bool $caseSensitive = true;
	
	public function toVar($value) : ?string
	{
		return empty($value) ? null : self::serialize($value);
	}
	
	public function toValue($var = null)
	{
		return $var === null ? null : self::unserialize($var);
	}

	public function plugVar() : string
	{
		return self::serialize(['a' => '1']);
	}
	
	public function validate($value) : bool
	{
		if (!(parent::validate($this->getVar())))
		{
			return false;
		}
		return true;
	}
	
}
