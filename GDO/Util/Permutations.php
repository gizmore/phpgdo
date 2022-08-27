<?php
namespace GDO\Util;

/**
 * Permutation generator.
 * Works for mixed[mixed]?
 * If you have empty values, you have one permutation with an empty array.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Permutations
{
	private int $count = 0;
	private array $values;
	private array $lastPermutation;
	
	public function __construct(array $values)
	{
		$this->values = $values;
		$this->lastPermutation = [];
		foreach (array_keys($values) as $k)
		{
			$this->lastPermutation[$k] = 0;
		}
		$this->count = self::countPermutations($values);
	}
	
	public static function countPermutations(array $values) : int
	{
		$n = 1;
		foreach ($values as $value)
		{
			$n *= count($value);
		}
		return $n;
	}
	
	public function generate() : \Generator
	{
		yield $this->lastPermutation();

		for ($i = 1; $i < $this->count; $i++)
		{
			foreach ($this->values as $k => $v)
			{
				$p = $this->lastPermutation[$k];
				$p++;
				if ($p >= count($v))
				{
					$p = 0;
					$this->lastPermutation[$k] = $p;
				}
				else
				{
					$this->lastPermutation[$k] = $p;
					yield $this->lastPermutation();
					break;
				}
			}
		}
	}
	
	private function lastPermutation()
	{
		$back = [];
		foreach ($this->lastPermutation as $k => $v)
		{
			$values = $this->values[$k];
			$back[$k] = empty($values) ? null : $values[$v];
		}
		return $back;
	}
	
}
