<?php
namespace GDO\Core;

use GDO\Util\WS;

/**
 * A fixed decimal, database driven field.
 *
 * @version 7.0.2
 * @since 6.1.0
 *
 * @author gizmore
 * @see GDT_Float
 * @see GDT_Money
 */
class GDT_Decimal extends GDT_Int
{

	##############
	### Digits ###
	##############
	public int $digitsBefore = 9;
	public int $digitsAfter = 4;

	public function digitsBefore(int $before): self
	{
		return $this->digits($before, $this->digitsAfter);
	}

	public function digits(int $before, int $after): self
	{
		$this->digitsBefore = $before;
		$this->digitsAfter = $after;
		# compute step automatically nicely
		$step = $after < 1 ? 1 : floatval('0.' . str_repeat('0', $after - 1) . '1');
		return $after < 1 ? $this->step(1) : $this->step(sprintf("%.0{$after}f", $step));
	}

	public function digitsAfter(int $after): self
	{
		return $this->digits($this->digitsBefore, $after);
	}

	##############
	### Render ###
	##############

	public function renderHTML(): string
	{
		return GDT_Float::displayS($this->getVar(), $this->digitsAfter);
	}

	public function renderBinary(): string
	{
		return WS::wrDouble($this->getVar());
	}

	#############
	### Value ###
	#############

	public function plugVars(): array
	{
		return [
			[$this->getName() => '3.14'],
		];
	}

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		$var = $value === null ? null : sprintf("%.0{$this->digitsAfter}f", $value);
		return $var;
	}

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : round(floatval($var), $this->digitsAfter);
	}

	public function _inputToVar(?string $input): ?string
	{
		if ($input = parent::_inputToVar($input))
		{
			return GDT_Float::inputToVarS($input);
		}
		return null;
	}

}
