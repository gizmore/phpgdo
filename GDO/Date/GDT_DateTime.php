<?php
namespace GDO\Date;

use DateTime;
use GDO\Core\GDT_Template;

/**
 * A datetime column has a bigger range of dates compared to a GDT_Timestamp.
 *
 * @version 7.0.0
 * @since 6.11.2
 * @author gizmore
 */
class GDT_DateTime extends GDT_Date
{

	public string $format = Time::FMT_SHORT;

	public function renderForm(): string
	{
		return GDT_Template::php('Date', 'datetime_form.php', ['field' => $this]);
	}

	public function htmlValue(): string
	{
		$seconds = $this->getValue();
		$isodate = date('Y-m-d H:i:s', $seconds);
		return sprintf(' value="%s"', $isodate);
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		if ($value)
		{
			/** @var $value DateTime * */
			$value->setTimezone(Time::$UTC);
			return $value->format('Y-m-d H:i:s.u');
		}
		return null;
	}

// 	public function displayValue($var)
// 	{
// 	    return Time::displayDate($var);
// 	}

// 	public function _inputToVar($input)
// 	{
// 	    $input = str_replace('T', ' ', $input); # remove RFC decorations
// 	    $input = str_replace('Z', '', $input);
// 	    if (!$d = Time::parseDateTime($input))
// 	    {
// 	    	$d = Time::parseDateTimeDB($input);
// 	    }
// 	    $d->setTimezone(Time::$UTC); # convert to UTC
// 	    $var = $d->format('Y-m-d H:i:s.v'); # output UTC
// 	    return $var;
// 	}

	public function htmlClass(): string
	{
		return ' gdt-datetime';
	}

}
