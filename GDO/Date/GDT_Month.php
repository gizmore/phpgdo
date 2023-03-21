<?php
namespace GDO\Date;

/**
 * A date select that snaps to the beginning of a month.
 *
 * @version 6.11.2
 * @author gizmore
 */
final class GDT_Month extends GDT_Date
{

	public function _inputToVar($input)
	{
		$input = str_replace('T', ' ', $input);
		$input = str_replace('Z', '', $input);
		$time = Time::parseDate($input, Time::UTC);
		$input = Time::getDate($time, 'Y-m-01');
		return $input;
	}

}
