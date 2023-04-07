<?php
namespace GDO\Core;

use GDO\CLI\CLI;
use GDO\Core\Expression\Parser;

/**
 * An expression executes a command line.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
final class GDT_Expression extends GDT
{

	###############
	### Factory ###
	###############
	public self $parent;
	public GDT_Method $method;
	public string $line;
	public array $inputs = [];

	public static function fromLine(string $line): self
	{
		static $parser = new Parser();
		return $parser->parse($line);
	}

	public function parent(self $parent): self
	{
		$this->parent = $parent;
		return $this;
	}

	public function method(Method $method): self
	{
		$this->method = GDT_Method::make()->method($method);
		return $this;
	}

	############
	### Exec ###
	############

	public function line(string $line): self
	{
		$this->line = $line;
		return $this;
	}

	#############
	### Input ###
	#############

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
			return GDT_String::make()->var($ex->renderCLI() . CLI::renderCLIHelp($this->method->method));
		}
	}

	public function addInput(?string $key, $input): void
	{
		$this->method->addInput($key, $input);
		$this->inputs = $this->method->getInputs();
 		if ($key === null)
 		{
 			$this->inputs[] = $input;
 		}
 		else
 		{
 			$this->inputs[$key] = $input;
 		}
	}

	public function hasPositionalInput(): bool
	{
		return isset($this->inputs[0]);
	}

	public function applyInputs(): void
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
