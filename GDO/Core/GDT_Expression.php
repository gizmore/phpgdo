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
	public static function fromLine(string $line) : self
	{
		$parser = new Parser();
		return $parser->parse($line);
	}
	
	public self $parent;
	public function parent(self $parent) : self
	{
		$this->parent = $parent;
		return $this;
	}
	
	public GDT_Method $method;
	public function method(Method $method) : self
	{
		$this->method = GDT_Method::make()->method($method);
		return $this;
	}

	public string $line;
	public function line(string $line) : self
	{
		$this->line = $line;
		return $this;
	}
	
	############
	### Exec ###
	############
	public function execute()
	{
		if (GDO_LOG_REQUEST)
		{
			Logger::log('cli', $this->line);
		}
		$response = $this->method->execute();
		if ($response->hasError())
		{
			$help = trim(CLI::renderCLIHelp($this->method->method));
			$response->addField(GDT_String::make()->var($help));
		}
		return $response;
	}
	
	#############
	### Input ###
	#############
	public array $inputs = [];
	
	public function addInput(?string $key, $input) : void
	{
		if ($key === null)
		{
			$this->inputs[] = $input;
		}
		else
		{
			$this->inputs[$key] = $input;
		}
	}
	
	public function hasPositionalInput() : bool
	{
		return isset($this->inputs[0]);
	}
	
	public function applyInputs() : void
	{
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
