<?php
namespace GDO\Core;

use GDO\Core\Expression\Parser;

/**
 * An expression executes a command line.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class GDT_Expression extends GDT
{
	
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
	
	public function addInput(string $key, $input)
	{
		return $this->method->addField($key, $input);
	}

	##################
	### Expression ###
	##################
	public static function fromLine(string $line) : self
	{
		$parser = new Parser($line);
		return $parser->parse();
	}
	
	public function execute() : GDT
	{
		return $this->method->execute();
	}
	
}
