<?php
namespace GDO\Core;

final class GDT_Method extends GDT
{
	public Method $method;
	public function method(Method $method) : self
	{
		$this->method = $method;
		return $this;
	}
	
	public array $inputs = [];
	public function addInput(string $key, $input)
	{
		if ($key)
		{
			$this->inputs[$key] = $input;
		}
		else
		{
			$this->inputs[] = $input;
		}
	}
	
	public function execute() : GDT
	{
		$this->method->setInputs($this->inputs);
		return $this->method->execute();
	}
	
}
