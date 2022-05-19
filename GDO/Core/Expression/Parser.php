<?php
namespace GDO\Core\Expression;

use GDO\Core\GDT_Expression;
use GDO\Core\Method;
use GDO\Core\GDO_ParseError;
use GDO\Util\Strings;

/**
 * Parse an expression into an expression tree.
 * 
 * @see Method
 * @see GDT_Method
 * @see GDT_Expression
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
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
		return $this->parseB($current, $this->line);
	}
	
	private function parseB(GDT_Expression $current, string $line) : GDT_Expression
	{
		$l = $this->line;
		$i = 0;
		$len = mb_strlen($l);
		$method = $this->parseMethod($l, $i, $len);
		$current->method($method);
		$quotes = 0;
		$parant = 0; 
		$arg = '';
		for (; $i < $len;)
		{
			$c = $l[$i++];
			
			if ($quotes)
			{
				switch ($c)
				{
					case '\\':
						$c2 = $line[$i++];
						switch ($c2)
						{
							case "n": $arg .= "\n"; break;
							case "t": $arg .= "\t"; break;
							case '"': $arg .= $c2; break;
							default: $arg .= $c . $c2; break;
						}
						break;
						
					case '"':
						$quotes = 1 - $quotes;
						$this->addArg($current, $arg);
						break;
						
					default:
						$arg .= $c;
						break;
						
				}
				continue;
			}
			
			switch ($c)
			{
				case '$':
					$line2 = $this->parseLine($l, $i, $len);
					$new = GDT_Expression::make()->parent($current);
					$this->addArgExpr($current, $new);
					$this->parseB($new, $line2);
					break;
					
				case '"':
					$quotes = 1 - $quotes;
					break;
					
				case '\\':
					if ($quotes)
					{
						$c2 = $l[$i++];
						switch ($c2)
						{
							case "n": $arg .= "\n"; break;
							case "t": $arg .= "\t"; break;
							default: $arg .= $c2; break;
						}
					}
					else
					{
						$arg .= '\\';
					}
					break;
					
				case " ":
					if ($arg)
					{
						$this->addArg($current, $arg);
					}
					break;
					
				default:
					$arg .= $c;
					break;
					
			}
		}
		
		if ($quotes)
		{
			throw new GDO_ParseError('err_unclosed_quotes', [html($line)]);
		}
		
		if ($parant)
		{
			throw new GDO_ParseError('err_unclosed_parantheses', [html($line)]);
		}
		
		if ($arg)
		{
			$this->addArg($current, $arg);
		}
		return $current;
	}
	
	private function addArg(GDT_Expression $expression, string &$arg)
	{
		if (str_starts_with($arg, '--'))
		{
			$arg = substr($arg, 2);
			$key = Strings::substrTo($arg, '=', $arg);
			$input = Strings::substrFrom($arg, '=', '1');
		}
		else
		{
			$key = null;
			$input = $arg;
		}
		$arg = '';
		$expression->method->addInput($key, $input);
	}
	
	private function addArgExpr(GDT_Expression $expression, GDT_Expression $arg)
	{
		$expression->method->addInput($key, $arg);
	}

	private function parseMethod(string $line, int &$i, int $len) : Method
	{
		$parsed = '';
		$started = false;
		for (;$i < $len;)
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
			elseif ($c === '.')
			{
				$parsed .= '.';
			}
			else
			{
				break;
			}
		}
		return Method::getMethod($parsed);
	}
	
	private function parseLine(string $line, int &$i, int $len) : Method
	{
		$parant = 0;
		$quotes = 0;
		$parsed = '';
		$started = false;
		for (;$i < $len;)
		{
			$c = $line[$i++];
			
			if ($quotes)
			{
				switch ($c)
				{
					case '\\':
						$c2 = $line[$i++];
						switch ($c2)
						{
							case "n": $parsed .= "\n"; break;
							case "t": $parsed .= "\t"; break;
							case '"': $parsed .= $c2; break;
							default: $parsed .= $c . $c2; break;
						}
						break;
						
					case '"':
						$quotes = 1 - $quotes;
						break;
						
					default:
						$parsed .= $c;
						break;
				}
				continue;
			}
			
			switch ($c)
			{
				case '(':
					$parant++;
					$started = true;
					break;
				case ')':
					$parant--;
					break 2;
				case '"':
					$quotes = 1 - $quotes;
					break;
			}
		}
		
		if (!$started)
		{
			throw new GDO_ParseError('err_invalid_cli_nested_line', [html($line)]);
		}

		if ($parant)
		{
			throw new GDO_ParseError('err_unclosed_parantheses', [html($line)]);
		}
			
		if ($quotes)
		{
			throw new GDO_ParseError('err_unclosed_quotes', [html($line)]);
		}
		
		return $parsed;
	}
	
}
