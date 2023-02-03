<?php
namespace GDO\Core;

/**
 * 
 * 
 * @author gizmore
 * @since 7.0.2
 */
final class GDT_RegEx extends GDT_String
{

	public function inputToVar($input): ?string
	{
		if ($input === null)
		{
			return null;
		}
		if ($input === '')
		{
			return '';
		}
		if ($input[0] === '/')
		{
			return $input;
		}
		return "/{$input}/";
	}

}
