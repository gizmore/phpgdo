<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * An enum without internationalization.
 * For example Used in GDT_FontWeight and JQueryUI Theme selector.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 * @see GDT_Enum
 */
class GDT_EnumNoI18n extends GDT_Enum
{

	public function displayVar(string $var = null): string
	{
		return $var === null ? t('none') : $var;
	}

}
