<?php
namespace GDO\Core;

/**
 * An enum without internationalization. For example Used in GDT_FontWeight and JQueryUI Theme selector. 
 * 
 * @see GDT_Enum
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
class GDT_EnumNoI18n extends GDT_Enum
{
	public function renderCell() : string { return $this->getVar(); }
	public function enumLabel($enumValue=null) : string { return $enumValue; }
	
}
