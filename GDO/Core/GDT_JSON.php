<?php
namespace GDO\Core;

/**
 * Datatype that uses JSON encoding to store arbitrary data in a column.
 * If you write a method to return json data, use GDT_Array.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 * @see GDT_Array
 * @see GDO_Session
 */
class GDT_JSON extends GDT_Text
{
	public bool $caseSensitive = true;

	public function getDefaultName() : string { return 'data'; }
    
	public static function encode($data) : ?string { return @json_encode($data, GDO_JSON_DEBUG?JSON_PRETTY_PRINT:0); }
	public static function decode(string $string) : ?array { return @json_decode($string, true); }
	
	public function toVar($value) : ?string { return $value === null ? null : self::encode($value); }
	public function toValue($var = null) { return $var === null ? null : self::decode($var); }

	public function renderJSON()
	{
		return $this->getValue();
	}
	
// 	public static function with(array $data) : self
// 	{
// 		return self::make()->value($data);
// 	}

	public function validate($value) : bool
	{
		if (!$this->validateNull($value))
		{
			return false;
		}
		if (!$this->validateLength($this->getVar()))
		{
			return false;
		}
		return true;
	}
	
	############
	### Test ###
	############
	public function plugVars() : array
	{
		return [
			[$this->name => '["one","two","three"]'],
		];
	}

}
