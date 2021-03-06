<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\UI\WithLabel;
use GDO\DB\Query;
use GDO\Table\WithOrder;
use GDO\Core\Application;
use GDO\Core\GDT_DBField;
use GDO\Core\GDO;

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
 * @version 6.11.2
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
	public function toValue(string $var = null)
	{
	    if ($var)
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
	
	/**
	 * @param int $secondsAgo
	 * @return self
	 */
	public function initialAgo($secondsAgo)
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
	public function format(string $format) : self
	{
		$this->format = $format;
		return $this;
	}
	
	###############
	### Min/Max ###
	###############
	/**
	 * @param int $duration
	 * @return \GDO\Date\GDT_Timestamp
	 */
	public function minAge($duration) { return $this->minTimestamp(Application::$TIME - $duration); }
	public function maxAge($duration) { return $this->maxTimestamp(Application::$TIME + $duration); }
	
	public $minDate;
	public function minTimestamp($minTimestamp)
	{
		return $this->minDate(Time::getDate($minTimestamp));
	}
	public function minDate($minDate)
	{
		$this->minDate = $minDate;		
		return $this;
	}
	
	public $maxDate;
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

	##############
	### Millis ###
	##############
	# @TODO rename $millis to $precision or $decimals in GDT_Timestamp.
	public int $millis = 3;
	public function millis(int $millis=3) : self
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
		if ( ($value === null) && (!$this->notNull) )
		{
			return true;
		}
		
		/** @var $value \DateTime **/
		if ($this->minDate !== null)
		{
		    if ($value->diff($this->minDate) < 0)
		    {
    		    return $this->error('err_min_date', [
    		        Time::displayDate($this->minDate, $this->getDateFormat())]);
		    }
		}
		
		if ($this->maxDate !== null)
		{
		    if ($value->diff($this->maxDate) > 0)
		    {
		        return $this->error('err_max_date', [
		            Time::displayDate($this->maxDate, $this->format)]);
		    }
		}

		return parent::validate($value);
	}
	
	##############
	### Column ###
	##############
	public function gdoColumnNames()
	{
		return [$this->name];
	}
	
	public function gdoColumnDefine() : string
	{
		return "{$this->identifier()} TIMESTAMP({$this->millis}){$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
		return Time::displayDateTime(
			Time::parseDateTimeDB($this->getVar()),
			$this->format);
	}
	public function renderForm() : string { return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]); }
	public function renderAge() : string { return Time::displayAge($this->getVar()); }
	public function renderCLI() : string { return $this->renderLabel() . ': ' . $this->getVar(); }
	public function renderJSON() { return Time::getTimestamp($this->getVar()) * 1000; }
	public function displayVar(string $var = null) : string
	{
		if ($dt = Time::parseDateTimeDB($var))
		{
			return Time::displayDateTime($dt, $this->format, '');
		}
		return '';
	}
	
	public function inputToVar($input = null) : ?string
	{
		if ($input === null)
		{
			return null;
		}
		if (!is_numeric($input))
		{
			if (preg_match('#^\\d{4}-\\d{2}-\\d{2}#', $input))
			{
				$input = Time::parseDateTimeDB($input);
			}
			else
			{
				$input = str_replace('T', ' ', $input);
				$input = str_replace('Z', '', $input);
				$input = Time::parseDateTime($input);
			}
		}
		else
		{
			$input /= 1000.0;
			$input = Time::getDateTime($input);
		}
		return $input ? $input->format("Y-m-d H:i:s.v") : null;
	}
	
	##############
	### Config ###
	##############
	public function configJSON() : array
	{
		return array_merge(parent::configJSON(), [
			'dateStartView' => $this->dateStartView,
			'format' => $this->format,
			'minDate' => $this->minDate,
			'maxDate' => $this->maxDate,
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
	public function filterVar(string $key=null)
	{
		return [];
	}
	
	public function renderFilter($f) : string
	{
		return GDT_Template::php('Date', 'timestamp_filter.php', ['field' => $this, 'f' => $f]);
	}
	
	public function filterQuery(Query $query, $rq=null) : self
	{
		$filter = $this->filterVar($rq);
		if ($filter)
		{
			if ($condition = $this->searchQuery($query, $filter, true))
			{
				$this->filterQueryCondition($query, $condition);
			}
		}
		return $this;
	}
	
	##############
	### Search ###
	##############
	public function searchQuery(Query $query, $searchTerm, $first)
	{
		return $this->searchCondition($searchTerm);
	}
	
	protected function searchCondition($searchTerm)
	{
		$searchTerm = GDO::escapeSearchS($searchTerm);
		return "{$this->name} LIKE '%{$searchTerm}%'";
	}
	
}
