<?php
declare(strict_types=1);
namespace GDO\Date;

use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;

/**
 * Duration field int in seconds.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 */
class GDT_Duration extends GDT_String
{

	public ?int $max = 24;

	public int $encoding = self::ASCII;

	public string $icon = 'time';

	public string $pattern = '/^(?:[\\.0-9 ]+[sminohdwy]{0,2} *)+$/iD';

	public int $minDuration = 0;

	#################
	### Min / Max ###
	#################
	public ?int $maxDuration = null;

	public function defaultLabel(): self
	{
		return $this->label('duration');
	}

	public function min(?int $min): static
	{
		$this->minDuration = $min;
		return $this;
	}

	public function max(?int $max): static
	{
		$this->maxDuration = $max;
		return $this;
	}

	###################
	### Var / Value ###
	###################
	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : Time::humanToSeconds($var);
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value === null ? null : Time::humanDuration($value);
	}

	##############
	### Render ###
	##############
	public function renderHTML(): string
	{
		return html($this->getVar());
	}

	public function renderForm(): string
	{
		return GDT_Template::php('Date', 'form/duration.php', ['field' => $this]);
	}

	################
	### Validate ###
	################
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		if ($value < $this->minDuration)
		{
			return $this->error('err_min_duration', [$this->minDuration]);
		}
		if (isset($this->maxDuration) && ($value > $this->maxDuration))
		{
			return $this->error('err_max_duration', [$this->maxDuration]);
		}
		return true;
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => '500ms'],
		];
	}

}
