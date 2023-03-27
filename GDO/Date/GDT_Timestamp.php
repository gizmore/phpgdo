<?php
declare(strict_types=1);
namespace GDO\Date;

use DateTime;
use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\GDT_DBField;
use GDO\Core\GDT_Method;
use GDO\Core\GDT_Template;
use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\Table\WithOrder;
use GDO\UI\WithLabel;

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
 *
 * @TODO: DateTimes transfer as string for the websocket protocol.
 *
 * @version 7.0.3
 * @since 6.0.7
 * @author gizmore
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
	public string $dateStartView = 'month';
	public string $format = Time::FMT_SHORT;
	public ?string $minDate = null;
	public ?string $maxDate = null;
	public int $millis = 3;
	public bool $defaultNow = false;

	#####################
	### Starting view ###
	#####################

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : Time::parseDateDB($var);
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value === null ? null : Time::displayTimestamp($value, 'db', '', Time::UTC);
	}

	public function initialSnap(int $mod): static
	{
		$time = Application::$TIME;
		$time = $time - ($time % $mod) + $mod;
		return $this->initialValue($time);
	}

	##############
	### Format ###
	##############

	public function initialNow(): static
	{
		return $this->initialAgo(0);
	}

	public function initialAgo(int $secondsAgo): static
	{
		return $this->initial(Time::getDate(Application::$MICROTIME - $secondsAgo));
	}

	###############
	### Min/Max ###
	###############

	public function getDate(): ?string
	{
		return $this->getVar();
	}

	/**
	 * Validate a Datetime.
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value))
		{
			return false;
		}

		if ($value === null)
		{
			return true;
		}

		/** @var DateTime $value * */
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

	public function plugVars(): array
	{
		return [
			[$this->name => Time::getDate()],
		];
	}

	public function renderHTML(): string
	{
		return Time::displayDateTime(
			Time::parseDateTimeDB($this->getVar()),
			$this->format);
	}

	public function renderForm(): string { return GDT_Template::php('Date', 'datetime_form.php', ['field' => $this]); }

	public function renderCLI(): string { return $this->renderLabel() . ': ' . $this->getVar(); }

	public function renderJSON(): array|string|null
	{
		return (string) (Time::getTimestamp($this->getVar()) * 1000.0);
	}

	public function displayVar(string $var = null): string
	{
		if ($dt = Time::parseDateTimeDB($var))
		{
			return Time::displayDateTime($dt, $this->format, '');
		}
		return GDT::EMPTY_STRING;
	}

	public function inputToVar(array|string|null|GDT_Method $input): ?string
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
	### Millis ###
	##############
	# @TODO rename $millis to $precision or $decimals in GDT_Timestamp.

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'dateStartView' => $this->dateStartView,
			'format' => $this->format,
			'minDate' => $this->minDate,
			'maxDate' => $this->maxDate,
			'millis' => $this->millis,
		]);
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Date', 'timestamp_filter.php', ['field' => $this, 'f' => $f]);
	}

	###########
	### Now ###
	###########

	public function filterQuery(Query $query, GDT_Filter $f): static
	{
		$filter = $this->filterVar($f);
		if ($filter)
		{
			$this->searchQuery($query, $filter);
		}
		return $this;
	}

	public function startWithYear(): static
	{
		$this->dateStartView = 'year';
		return $this;
	}

	################
	### Validate ###
	################

	public function startWithMonth(): static
	{
		$this->dateStartView = 'month';
		return $this;
	}

	public function format(string $format): static
	{
		$this->format = $format;
		return $this;
	}

	##############
	### Render ###
	##############

	public function minAge(int $duration): static
	{
		return $this->minTimestamp(Application::$TIME - $duration);
	}

	public function minTimestamp($minTimestamp): static
	{
		return $this->minDate(Time::getDate($minTimestamp));
	}

	public function minDate(?string $minDate): static
	{
		$this->minDate = $minDate;
		return $this;
	}

	public function maxAge(int $duration): self
	{
		return $this->maxTimestamp(Application::$TIME + $duration);
	}

	public function maxTimestamp($maxTimestamp): static
	{
		return $this->maxDate(Time::getDate($maxTimestamp));
	}

	public function maxDate($maxDate): static
	{
		$this->maxDate = $maxDate;
		return $this;
	}

	public function maxNow(): static
	{
		return $this->maxDate(Time::getDate());
	}

	##############
	### Config ###
	##############

	public function minNow(): static
	{
		return $this->minTimestamp(Application::$TIME);
	}

	public function millis(int $millis = 3): static
	{
		$this->millis = $millis;
		return $this;
	}

	##############
	### Filter ###
	##############

	public function defaultNow($defaultNow = true): static
	{
		$this->defaultNow = $defaultNow;
		return $this->initial(Time::getDate());
	}

	public function renderAge(): string
	{
		return Time::displayAge($this->getVar());
	}

}
