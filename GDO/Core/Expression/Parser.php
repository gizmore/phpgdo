<?php
namespace GDO\Core\Expression;

use GDO\Core\GDT_Expression;
use GDO\Core\Method;
use GDO\Util\Strings;
use GDO\Core\GDO_Error;

/**
 * Parse a CLI expression into an expression tree for execution.
 * A grad student probably would have pulled a lexer and AST stuff ;)
 * 
 * The syntax is a bit weird, first named params, then positional required params.
 * Required params are seperated by comma.
 * 
 * Syntax:
 * 
 * @example gdo cli.echo hi there.
 * @example gdo math.calc 1+2+3
 * @example concat a,$(wget https://google.de) # => a<!DOCTYPE....
 * @example add $(add 1,2),$(add 3,4) # => 10
 * 
 * @example mail giz;hi there(howdy;$(concat );   wget --abc ssh://)
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 * @see Method
 * @see GDT_Method
 * @see GDT_Expression
 */
final class Parser
{
	const SPACE = ' '; # seperates named args.
	const QUOTES = '"';
	const CMD_PREAMBLE = '$';
	const CMD_BEGIN = '(';
	const CMD_ENDIN = ')';
	const ARG_SEPARATOR = ',';
	const VAL_SEPERATOR = '=';
	const ESCAPE_CHARACTER = '\\';
	
	private string $line;
	
	public function parse(string $line) : GDT_Expression
	{
		$this->line = $line;
		$current = GDT_Expression::make();
		return $this->parseB($current->line($line), $this->line);
	}
	
	###############
	### Private ###
	###############
	private function parseB(GDT_Expression $current, string $line) : GDT_Expression
	{
		$i = 0;
		$l = $line;
		$len = mb_strlen($l);
		$method = $this->parseMethod($l, $i, $len);
		$current->method($method);
		$current->method->clibutton();
		$arg = '';
		for (; $i < $len;)
		{
			$c = $l[$i++];
			
			switch ($c)
			{
				case self::CMD_PREAMBLE:
					if (strlen($arg) === 0)
					{
						if (@$l[$i] === '(')
						{
							$i++;
							$line2 = $this->parseLine($l, $i, $len);
							$new = GDT_Expression::make()->parent($current);
							$this->parseB($new, $line2);
							$this->addArgExpr($current, $new);
							break;
						}
					}
					$arg .= $c;
					break;
				
				case self::ESCAPE_CHARACTER:
					$c2 = $l[$i++];
					switch ($c2)
					{
						case 'n': case 'N': $arg .= "\n"; break;
						default: $arg .= $c2; break;
					}
					break;

				case self::ARG_SEPARATOR:
					$c2 = $l[$i];
					if ($c2 === self::ARG_SEPARATOR)
					{
						$arg .= $l[$i++];
					}
					elseif ($arg)
					{
						$this->addArg($current, $arg);
					}
					break;
					
				case self::SPACE:
					# space means next arg, if not yet positional
					$arg .= $c;
					if (!$current->hasPositionalInput())
					{
						if (str_starts_with($arg, '--'))
						{
							$this->addArg($current, $arg);
						}
					}
					break;

				default:
					$arg .= $c;
					break;
			}
		}
		if ($arg)
		{
			$this->addArg($current, $arg);
		}
		
		$current->applyInputs();
		
		return $current;
	}
	
	private function addArg(GDT_Expression $expression, string &$arg) : void
	{
		if (str_starts_with($arg, '--'))
		{
			if ($expression->hasPositionalInput())
			{
				throw new GDO_Error('err_positional_after_named_parameter', [html($arg)]);
			}
			$arg = substr($arg, 2);
			$arg = Strings::substrTo($arg, self::SPACE, $arg);
			$key = Strings::substrTo($arg, self::VAL_SEPERATOR, $arg);
			$input = Strings::substrFrom($arg, self::VAL_SEPERATOR, '1');
		}
		else
		{
			$key = null;
			$input = $arg;
		}
		$arg = '';
		$expression->addInput($key, $input);
	}
	
	private function addArgExpr(GDT_Expression $expression, GDT_Expression $arg) : void
	{
		$expression->addInput(null, $arg->method);
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
			if ($c === self::QUOTES)
			{
				continue;
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
		
		$method = Method::getMethod($parsed, true);
		
		return $method;
	}
	
	/**
	 * Parse an additional line within parantheses.
	 */
	private function parseLine(string $line, int &$i, int $len) : string
	{
		$parsed = '';
		for (;$i < $len;)
		{
			$c = $line[$i++];
			switch ($c)
			{
				case self::ESCAPE_CHARACTER:
					$c2 = $line[$i++];
					$parsed .= $c2;
					break;
				
				case self::CMD_END:
					break 2;
					
				default:
					$parsed .= $c;
					break;
			}
		}
		return $parsed;
	}
	
}
