<?php
namespace GDO\Core;

/** ______________
 * (Need. Input.=|
 *  , (c)gizmore_)
 * [=]
 *°|.|´
 * / \
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @license GDOv7-LICENSE
 */
trait WithInput
{
	/**
	 * An input can also be a GDT_Method, for nested expressions.
	 * @var GDT|string[]
	 */
	public array $inputs;
	
	/**
	 * Set all inputs to the fixed inputs parameter.
	 * @param GDT|string[] $inputs
	 */
	public function inputs(array $inputs=null) : self
	{
		if ($inputs === null)
		{
			unset($this->inputs);
		}
		else
		{
			$this->inputs = $inputs;
		}
		return $this;
	}
	
	public function addInput(string $key, $var) : self
	{
		$this->inputs[$key] = $var;
		return $this;
	}
	
	public function getInputs() : array
	{
		return isset($this->inputs) ? $this->inputs : GDT::EMPTY_ARRAY;
	}
	
	public function hasInput() : bool
	{
		if ($name = $this->getName())
		{
			if (isset($this->inputs[$name]))
			{
				return $this->inputs[$name] !== null;
			}
		}
		return false;
	}
	
	public function getInput() : ?string
	{
		$key = $this->getName();
		return isset($this->inputs[$key]) ? $this->inputs[$key] : null;
	}
	
}
