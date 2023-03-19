<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\Core\GDT_String;

/**
 * Duration field int in seconds.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
class GDT_Duration extends GDT_String
{
	public function defaultLabel(): static { return $this->label('duration'); }
	
	public int $max = 16;
	public int $encoding = self::ASCII;
	public string $icon = 'time';
	public string $pattern = '/^(?:[\\.0-9 ]+[sminohdwy]{0,2} *)+$/iD';
	
	#################
	### Min / Max ###
	#################
	public int $minDuration = 0;
	public function min(int $minDuration): static
	{
		$this->minDuration = $minDuration;
		return $this;
	}
	
	public int $maxDuration;
	public function max(int $maxDuration): static
	{
		$this->maxDuration = $maxDuration;
		return $this;
	}

	###################
	### Var / Value ###
	###################
	public function toValue($var = null)
	{
	    return $var === null ? null : Time::humanToSeconds($var);
	}
	
	public function toVar($value) : ?string
	{
	    return $value === null ? null : Time::humanDuration($value);
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		return html($this->getVar());
	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('Date', 'form/duration.php', ['field' => $this]);
	}

	################
	### Validate ###
	################
	public function validate($value) : bool
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
			return $this->error('err_max_duration', [$this->minDuration]);
		}
		return true;
	}
	
	public function plugVars() : array
	{
		return [
			[$this->getName() => '1s'],
		];
	}
	
}
