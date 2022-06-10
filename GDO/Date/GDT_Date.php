<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;

/**
 * A Date is like a Datetime, but a bit older, so we start with year selection.
 * An example is the release date of a book, or a birthdate.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 * @see GDT_Time
 * @see GDT_Timestamp
 * @see GDT_DateTime
 * @see GDT_Duration
 * @see Module_Birthday
 */
class GDT_Date extends GDT_Timestamp
{
	public string $icon = 'calendar';
	public string $format = Time::FMT_DAY;
	public string $dateStartView = 'year';
	
	##########
	### DB ###
	##########
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} DATE {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	###################
	### Var / Value ###
	###################
	public function toVar($value) : ?string
	{
	    return $value ? $value->format('Y-m-d') : null;
	}
	
	public function _inputToVar($input)
	{
		$input = str_replace('T', ' ', $input);
		$input = str_replace('Z', '', $input);
		$time = Time::parseDate($input, Time::UTC);
		$input = Time::getDate($time, 'Y-m-d');
		return $input;
	}
	
	public function toValue(string $var = null)
	{
	    return empty($var) ? null : Time::parseDateTimeDB($var);
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
		return Time::displayDate($this->getVar());
// 		return $this->renderCellSpan($this->display());
	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('Date', 'form/date.php', ['field'=>$this]);
	}
	
// 	public function displayValue($value)
// 	{
// 		return Time::displayDateTime($value, $this->format);
// 	}

}
