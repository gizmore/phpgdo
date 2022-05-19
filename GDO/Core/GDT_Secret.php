<?php
namespace GDO\Core;

/**
 * A secret is a config string that is shown as asterisks to the user.
 * In various transport protocols (json, websocket) this field is not transmitted.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 4.0.3
 */
class GDT_Secret extends GDT_String
{
    public function isSerializable() : bool { return false; }

    public bool $hidden = true;
    public int $encoding = self::ASCII;
	public bool $caseSensitive = true;
	
	public function renderCell() { return '********'; }

}