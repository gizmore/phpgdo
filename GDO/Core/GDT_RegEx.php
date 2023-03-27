<?php
namespace GDO\Core;

/**
 *
 *
 * @since 7.0.2
 * @author gizmore
 */
final class GDT_RegEx extends GDT_String
{

	public function inputToVar(array|string|null|GDT_Method $input): ?string
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
