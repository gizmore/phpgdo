<?php
namespace GDO\Core;

/**
 * A secret is a config string that is shown as asterisks to the user.
 * In most(all?) transport protocols (json, websocket) this field is not transmitted.
 * 
 * @see GDT_String
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 4.0.3
 */
class GDT_Secret extends GDT_String
{
    public bool $hidden = true;
	public bool $caseSensitive = true;
	
    public function isSerializable() : bool { return false; }

	public function renderHTML() : string { return '****'; }

}
