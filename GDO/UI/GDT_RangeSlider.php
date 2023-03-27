<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT_Template;

/**
 * Slider for range input with 2 handles.
 * In web1.0 themes, 2 inputs are used instead.
 * This GDT does not create a database column and is intended to be used in forms only.
 *
 * @version 7.0.3
 * @since 6.3.0
 * @author gizmore
 * @see GDT_Slider
 */
final class GDT_RangeSlider extends GDT_Slider
{

	public string $highName;

	###########
	### GDO ###
	###########
	public int $minRange = -1;

	###############
	### Options ###
	###############
	public int $maxRange = -1;

	public function renderForm(): string { return GDT_Template::php('UI', 'form/range_slider.php', ['field' => $this]); }

	public function getGDOData(): array { return [$this->name => $this->getLow(), $this->highName => $this->getHigh()]; }

	public function getLow() { return $this->getVal(0); }

	private function getVal($i)
	{
		$v = $this->getValue();
		return $v ? $v[$i] : $v;
	}

	public function getHigh() { return $this->getVal(1); }

	###################
	### Var / Value ###
	###################

	public function highName($highName): static
	{
		$this->highName = $highName;
		return $this;
	}

	public function minRange($minRange): static
	{
		$this->minRange = $minRange;
		return $this;
	}

	public function maxRange($maxRange): static
	{
		$this->maxRange = $maxRange;
		return $this;
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string { return $value === null ? null : json_encode($value); }

	public function initialLow() { return $this->var ? json_decode($this->var)[0] : null; }

	public function initialHigh() { return $this->var ? json_decode($this->var)[1] : null; }

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array { return $var === null ? null : json_decode($var); }

	public function initialValue($value): static
	{
		$this->initial = $this->var = $this->toVar($value);
		return parent::initialValue($value);
	}


	################
	### Validate ###
	################
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		[$lo, $hi] = $value;
		if ((parent::validate($lo)) && (parent::validate($hi)))
		{
			$range = $hi - $lo;
			if (($this->minRange >= 0) && ($range < $this->minRange))
			{
				return $this->error('err_range_underflow', [$this->minRange]);
			}
			if (($this->maxRange >= 0) && ($range > $this->maxRange))
			{
				return $this->error('err_range_exceed', [$this->maxRange]);
			}
			return true;
		}
		return false;
	}

}
