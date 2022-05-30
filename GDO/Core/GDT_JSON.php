<?php
namespace GDO\Core;

/**
 * Datatype that uses JSON encoding to store arbitrary data in a column.
 * If you write a method to return json, use GDT_Array.
 * 
 * @see GDT_Array
 * 
 * @author gizmore
 * @see GDO_Session
 * @version 7.0.0
 * @since 6.5.0
 */
class GDT_JSON extends GDT_Text
{
	public bool $caseSensitive = true;

	public function getDefaultName() : string { return 'data'; }
    
	public static function encode($data) : string { return json_encode($data, GDO_JSON_DEBUG?JSON_PRETTY_PRINT:0); }
	public static function decode(string $string) : array { return json_decode($string, true); }
	
	public function toVar($value) : ?string { return $value === null ? null : self::encode($value); }
	public function toValue(string $var = null) { return $var === null ? null : self::decode($var); }

	public function renderJSON()
	{
		return $this->getValue();
	}

}
