<?php
namespace GDO\Core;

use GDO\Core\Expression\Parser;
use GDO\CLI\CLI;

/**
 * An expression executes a command line.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
final class GDT_Expression extends GDT
{
	###############
	### Factory ###
	###############
	public static function fromLine(string $line): static
	{
		static $parser = new Parser();
		return $parser->parse($line);
	}
	
	public self $parent;
	public function parent(self $parent): static
	{
		$this->parent = $parent;
		return $this;
	}
	
	public GDT_Method $method;
	public function method(Method $method): static
	{
		$this->method = GDT_Method::make()->method($method);
		return $this;
	}

	public string $line;
	public function line(string $line): static
	{
		$this->line = $line;
		return $this;
	}
	
	############
	### Exec ###
	############
	public function execute()
	{
		try
		{
			if (GDO_LOG_REQUEST)
			{
				Logger::log('cli', $this->line);
			}
			return $this->method->execute();
		}
		catch (GDO_ArgException|GDO_CRUDException $ex)
		{
			return $ex->renderCLI() . trim(CLI::renderCLIHelp($this->method->method));
		}
	}
	
	#############
	### Input ###
	#############
	public array $inputs = [];
	
	public function addInput(?string $key, $input) : void
	{
		$this->method->addInput($key, $input);
		$this->inputs = $this->method->getInputs();
// 		if ($key === null)
// 		{
// 			$this->inputs[] = $input;
// 		}
// 		else
// 		{
// 			$this->inputs[$key] = $input;
// 		}
	}
	
	public function hasPositionalInput() : bool
	{
		return isset($this->inputs[0]);
	}
	
	public function applyInputs() : void
	{
		$this->method->inputs($this->inputs);
		$this->method->method->inputs($this->inputs);
		
// 		$cache = $this->method->method->gdoParameterCache();
		
// 		$pos = 0;
// 		foreach ($this->inputs as $key => $input)
// 		{
// 			if (is_numeric($key))
// 			{
// 				foreach ($cache as $gdt)
// 				{
// 					if ()
// 					{
						
// 					}
// 				}
// 			}
// 		}
	}
	
}
