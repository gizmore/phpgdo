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
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithInput
{
	/**
	 * @var GDT|string[]
	 */
	public array $inputs;
	
// 	/**
// 	 * Generally input capable?
// 	 */
// 	public function hasInputs() : bool
// 	{
// 		return true;
// 	}
	
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
		return isset($this->inputs) ? $this->inputs : GDT::EMPTY_GDT_ARRAY;
	}
	
	public function hasInput(string $key=null) : bool
	{
		if ($name = $this->getName())
		{
			return isset($this->inputs[$name]);
		}
		return false;
	}
	
// 	public function addInputs(array $inputs) : self
// 	{
// 		foreach ($inputs as $key => $input)
// 		{
// 			$this->addInput($key, $input);
// 		}
// 		if ($this->hasFields())
// 		{
// 			foreach ($this->getFields() as $gdt)
// 			{
// 				$gdt->addInputs($inputs);
// 			}
// 		}
		
// 		return $this;
// 	}
	
	public function getInput(string $key=null)
	{
		return isset($this->inputs[$key]) ? $this->inputs[$key] : null;
// 		if ($key === $this->getName())
// 		{
// 			return isset($this->input) ? $this->input : $this->var;
// 		}
// 		if (!isset($this->inputs))
// 		{
// 			return null;
// 		}
// 		if (!isset($this->inputs[$key]))
// 		{
// 			return null;
// 		}
		
// 		$input = $this->inputs[$key];
		
// 		if (is_array($input))
// 		{
// 			return json_encode($input);
// 		}
		
// 		return $input;
	}
	
	
// 	/**
// 	 * Add a single input.
// 	 * 
// 	 * @param string $key
// 	 * @param Method|string $input
// 	 * @return self
// 	 */
// 	public function addInput(?string $key, $input) : self
// 	{
// 		# Add input to the field
// 		if ( ($this->getName() === $key) && ($key !== null) )
// 		{
// 			$this->input($input);
// 		}

// 		# Add inputs to this
// 		if (!isset($this->inputs))
// 		{
// 			$this->inputs = [];
// 		}
// 		if ($key !== null)
// 		{
// 			$this->inputs[$key] = $input;
// 		}
// 		else
// 		{
// 			$this->inputs[] = $input;
// 		}
		
// // 		$this->valueConverted = false;
// 		return $this;
// 	}
	
// 	public function hasInput(string $key=null) : bool
// 	{
// // 		return Application::$INSTANCE->hasInput($key);
// 		if (isset($this->input))
// 		{
// 			return true;
// 		}
// 		if ($key === null)
// 		{
// 			return isset($this->inputs);
// 		}
// 		return isset($this->inputs[$key]);
// 	}
	
}
