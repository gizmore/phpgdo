<?php
namespace GDO\Core;

/**
 * An enum without internationalization.
 * For example Used in GDT_FontWeight and JQueryUI Theme selector. 
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 * @see GDT_Enum
 */
class GDT_EnumNoI18n extends GDT_Enum
{
	public function renderCell() : string
	{
		$var = $this->getVar();
		return $var ? $var : '';
	}
	
	public function enumLabel(string $enumValue=null) : string
	{
		return $enumValue;
	}
	
}
