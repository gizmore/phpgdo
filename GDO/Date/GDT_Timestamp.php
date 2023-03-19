<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\UI\WithLabel;
use GDO\DB\Query;
use GDO\Table\WithOrder;
use GDO\Core\Application;
use GDO\Core\GDT_DBField;
use GDO\Core\GDO;
use GDO\Table\GDT_Filter;
use GDO\Core\GDT;

/**
 * The GDT_Timestamp field is the baseclass for all datefields.
 * The var type is a mysql date.
 * The value type is an integer/timestamp.
 * For DateTimes the value type is a DateTime.
 * 
 * - control min/max dates via age or a fixed date.
 * - control precision with $millis (Default %.03f).
 * 
 * GDT_Timestamp transfers as f32 for the binary protocol.
 * @TODO: DateTimes transfer as string for the websocket protocol. 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.7
 */
class GDT_Timestamp extends GDT_DBField
{
	use WithLabel;
	use WithOrder;
	use WithTimezone;
	
	public string $icon = 'time';

	#############
	### Value ###
	#############
	public function toValue($var = null)
	{
	    if ($var !== null)
	    {
	        return Time::parseDateDB($var);
	    }
	}
	
	public function toVar($value) : ?string
	{
		return $value === null ? null : Time::displayTimestamp($value, 'db', null, Time::UTC);
	}
	
	public function getVar()
	{
		if ($var = parent::getVar())
		{
			$var = trim($var);
		}
		return $var ? $var : null;
	}
	
	public function initialSnap($mod)
	{
		$time = Application::$TIME;
		$time = $time - ($time % $mod) + $mod;
		return $this->initialValue($time);
	}
	
	public function initialNow()
	{
	    return $this->initialAgo(0);
	}
	
	public function initialAgo(int $secondsAgo): static
	{
	    return $this->initial(Time::getDate(Application::$MICROTIME - $secondsAgo));
	}

	#####################
	### Starting view ###
	#####################
	public string $dateStartView = 'month';
	public function startWithYear()
	{
		$this->dateStartView  = 'year';
		return $this;
	}
	public function startWithMonth()
	{
		$this->dateStartView  = 'month';
		return $this;
	}
	
	##############
	### Format ###
	##############
	public string $format = Time::FMT_SHORT;
	public function format(string $format): static
	{
		$this->format = $format;
		return $this;
	}
	
	###############
	### Min/Max ###
	###############
	public function minAge(int $duration): static { return $this->minTimestamp(Application::$TIME - $duration); }
	public function maxAge(int $duration): static { return $this->maxTimestamp(Application::$TIME + $duration); }
	
	public string $minDate;
	public string $maxDate;
	
	public function minTimestamp($minTimestamp)
	{
		return $this->minDate(Time::getDate($minTimestamp));
	}
	
	public function minDate($minDate)
	{
		$this->minDate = $minDate;		
		return $this;
	}
	
	public function maxTimestamp($maxTimestamp)
	{
		return $this->maxDate(Time::getDate($maxTimestamp));
	}
	
	public function maxDate($maxDate)
	{
		$this->maxDate = $maxDate;
		return $this;
	}
	
	public function maxNow()
	{
	    return $this->maxDate(Time::getDate());
	}
	
	public function minNow(): static
	{
		return $this->minTimestamp(Application::$TIME);
	}
	
	##############
	### Millis ###
	##############
	# @TODO rename $millis to $precision or $decimals in GDT_Timestamp.
	public int $millis = 3;
	public function millis(int $millis=3): static
	{
	    $this->millis = $millis;
	    return $this;
	}
	
	###########
	### Now ###
	###########
	public $defaultNow = false;
	public function defaultNow($defaultNow=true)
	{
	    $this->defaultNow = $defaultNow;
	    return $this->initial(Time::getDate());
	}
	
	################
	### Validate ###
	################
	/**
	 * Validate a Datetime.
	 */
	public function validate($value) : bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		
		if ($value === null)
		{
			return true;
		}
		
		/** @var $value \DateTime **/
		if (isset($this->minDate))
		{
			$t = Time::getTimestamp($this->minDate);
			$dt = Time::getDateTime($t);
			$dif = $value->diff($dt);
			$neg = $dif->invert; # Set to 1 if negative / 0 otherwise
			if (!$neg)
		    {
    		    return $this->error('err_min_date', [
    		        Time::displayDate($this->minDate, $this->format)]);
		    }
		}
		
		if (isset($this->maxDate))
		{
			$t = Time::getTimestamp($this->maxDate);
			$dt = Time::getDateTime($t);
			$dif = $value->diff($dt);
			$neg = $dif->invert; # Set to 1 if negative / 0 otherwise
			if ($neg)
		    {
		        return $this->error('err_max_date', [
		            Time::displayDate($this->maxDate, $this->format)]);
		    }
		}

		return true;
	}
	
	public function plugVars() : array
	{
		$name = $this->name;
		return [
			[$name => Time::getDate()],
		];
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return Time::displayDateTime(
			Time::parseDateTimeDB($this->getVar()),
			$this->format);
	}
	public function renderForm() : string { return GDT_Template::php('Date', 'datetime_form.php', ['field' => $this]); }
	public function renderAge() : string { return Time::displayAge($this->getVar()); }
	public function renderCLI() : string { return $this->renderLabel() . ': ' . $this->getVar(); }
	public function renderJSON() { return Time::getTimestamp($this->getVar()) * 1000; }
	public function displayVar(string $var = null) : string
	{
		if ($dt = Time::parseDateTimeDB($var))
		{
			return Time::displayDateTime($dt, $this->format, '');
		}
		return GDT::EMPTY_STRING;
	}
	
// 	public function renderCard() : string
// 	{
// 		die('XXXXX');
// 	}
	
	public function inputToVar($input) : ?string
	{
		if ($input === null)
		{
			return null;
		}
		
		# Not JS timestamp?
		if (!is_numeric($input))
		{
			$input = str_replace('T', ' ', $input);
			$input = str_replace('Z', '', $input);
			if (preg_match('#^\\d{4}-\\d{2}-\\d{2}#', $input))
			{
				$input = Time::parseDateTimeDB($input, null);
			}
			else
			{
				$input = Time::parseDateTime($input);
			}
		}
		else
		{
			# JS timestamp ms
			$input /= 1000.0;
			$input = Time::getDateTime($input);
		}
		
		return $input ? Time::displayDateTime($input, 'db', '', Time::UTC) : null;
	}
	
	##############
	### Config ###
	##############
	public function configJSON() : array
	{
		return array_merge(parent::configJSON(), [
			'dateStartView' => $this->dateStartView,
			'format' => $this->format,
			'minDate' => isset($this->minDate) ? $this->minDate : null,
			'minDate' => isset($this->maxDate) ? $this->maxDate : null,
		    'millis' => $this->millis,
		]);
	}
	
	public function getDate()
	{
	    return $this->getVar();
	}
	
	##############
	### Filter ###
	##############
	public function renderFilter(GDT_Filter $f) : string
	{
		return GDT_Template::php('Date', 'timestamp_filter.php', ['field' => $this, 'f' => $f]);
	}
	
	public function filterQuery(Query $query, GDT_Filter $f): static
	{
		$filter = $this->filterVar($f);
		if ($filter)
		{
			if ($condition = $this->searchQuery($query, $filter, true))
			{
				$this->filterQueryCondition($query, $condition);
			}
		}
		return $this;
	}
	
//	##############
//	### Search ###
//	##############
//	public function searchQuery(Query $query, $searchTerm, $first)
//	{
//		return $this->searchCondition($searchTerm);
//	}
//
//	protected function searchCondition($searchTerm)
//	{
//		$searchTerm = GDO::escapeSearchS($searchTerm);
//		return "{$this->name} LIKE '%{$searchTerm}%'";
//	}
	
}
