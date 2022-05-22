<?php
namespace GDO\Core;

/**
 * Need. input =]
 * 
 * [=]
 *  |´
 *  /\
 * 
 * @author gizmore
 * @version 7.0.3
 * @since 7.0.1
 */
trait WithInput
{
	/**
	 * @var GDT|string[]
	 */
	public array $inputs;
	
	/**
	 * Set all inputs to the fixed inputs parameter.
	 * @param GDT|string[] $inputs
	 */
	public function inputs(array $inputs) : self
	{
		$this->inputs = $inputs;
		return $this;
	}
	
	public function getInputs() : array
	{
		return isset($this->inputs) ? $this->inputs : GDT::EMPTY_GDT_ARRAY;
	}
	
	public function addInputs(array $inputs) : self
	{
		foreach ($inputs as $key => $input)
		{
			$this->addInput($key, $input);
		}
		return $this;
	}
	
	/**
	 * Add a single input.
	 * @param string $key
	 * @param Method|string $input
	 * @return self
	 */
	public function addInput(?string $key, $input) : self
	{
		if ($key)
		{
			$this->inputs[$key] = $input;
		}
		else
		{
			$this->inputs[] = $input;
		}
		return $this;
	}
	
}
