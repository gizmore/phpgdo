<?php
namespace GDO\Util;

/**
 * Permutation generator.
 * 
 * @author gizmore
 */
final class Permutations
{
	private array $values;
	
	public function __construct(array $values)
	{
		$this->values = $values;
		$this->lastPermutation = [];
		foreach (array_keys($values) as $k)
		{
			$this->lastPermutation[$k] = 0;
		}
	}
	
	private array $lastPermutation;
	
	public function generate()
	{
		yield $this->lastPermutation();
		
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
	
	private function lastPermutation()
	{
		$back = [];
		foreach ($this->lastPermutation as $k => $v)
		{
			$back[$k] = $this->values[$k][$v];
		}
		return $back;
	}
	
}
