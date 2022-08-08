<?php
namespace GDO\Core;

/**
 * An enum without internationalization.
 * For example Used in GDT_FontWeight and JQueryUI Theme selector. 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.1.0
 * @see GDT_Enum
 */
class GDT_EnumNoI18n extends GDT_Enum
{
	public function displayVar(string $var=null) : string
	{
		return $var === null ? t('none') : $var;
	}
	
}
