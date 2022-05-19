<?php
namespace GDO\Core;

/**
 * A GDT_Method holds a Method and inputs to bind.
 * An input s either a string or a GDT_Method.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class GDT_Method extends GDT
{
	public Method $method;
	public function method(Method $method) : self
	{
		$this->method = $method;
		return $this;
	}
	
	public array $inputs = [];
	public function addInput(?string $key, $input)
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
	
	public function exec() : GDT
	{
		$this->method->parameters($this->inputs);
		return $this->method->exec();
	}
	
	public function execute() : GDT
	{
		$this->method->parameters($this->inputs);
		return $this->method->execute();
	}
	
}
