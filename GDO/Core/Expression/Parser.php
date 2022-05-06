<?php
namespace GDO\Core\Expression;

use GDO\Core\Method;

/**
 * Parse an expression into an expression tree.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class Parser
{
	private $line;
	
	public function __construct(string $line)
	{
		$this->line = $line;
	}
	
	public function parse() : GDT_Expression
	{
		$current = GDT_Expression::make();
		$expressions = [$current];
		$e = 0;
		$quotes = 0;
		$dollar = false;
		$parant = 0; 
		$l = $this->line;
		$i = 0;
		$len = mb_strlen($l);
		$lines = [];
		for ($i = 0; $i < $len;)
		{
			$c = $l[$i++];
			
			switch ($c)
			{
				case '$':
					if (!$quotes)
					{
						$dollar = true;
						$method = $this->parseMethod($l, $i, $len);
						
						$current->addInput(, $input)
					}
					break;
					
				case '(':
					if (!$quotes)
					{
						$parant++;
						
					}
					break;
				case ')':
					if (!$quotes)
					{
						$parant++;
						
					}
					break;
					
				case '"':
					$quotes = 1 - $quotes;
					break;
					
			}
		}
		return $current;
	}

	private function parseMethod(string $line, int &$offset, int $len) : Method
	{
		$parsed = '';
		$i = $offset;
		$started = false;
		for ($i = $offset; $i < $len;)
		{
			$c = $line[$i++];
			if (ctype_space($c))
			{
				if ($started)
				{
					break;
				}
			}
			elseif (ctype_alnum($c))
			{
				$started = true;
				$parsed .= $c;
			}
			else
			{
				break;
			}
		}
		return Method::getMethod($parsed);
	}
	
}
