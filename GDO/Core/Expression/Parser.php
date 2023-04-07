<?php
declare(strict_types=1);
namespace GDO\Core\Expression;

use GDO\Core\GDO_NoSuchCommandError;
use GDO\Core\GDO_NoSuchMethodError;
use GDO\Core\GDT_Expression;
use GDO\Core\Method;
use GDO\Util\Strings;

/**
 * Parse a CLI expression into an expression tree for execution.
 * A grad student probably would have pulled a lexer and AST stuff ;)
 *
 * The syntax is a bit weird, first named params, then positional required params.
 * All parameters are seperated by comma.
 *
 * Syntax:
 *
 * @version 7.0.3
 * @since 7.0.0
 * @example gdo cli.echo hi there.
 * @example gdo math.calc 1+2+3
 * @example gdo cli.concat a,$(wget https://google.de) # => a<!DOCTYPE....
 * @example gdo math.add $(add 1,2),$(add 3,4) # => 10
 *
 * @example gdo mail giz,hi there(howdy;$(concat );   wget --abc ssh://)
 *
 * @author gizmore
 * @see Method
 * @see GDT_Method
 * @see GDT_Expression
 */
final class Parser
{

	final public const SPACE = ' '; # seperates named args.
	final public const QUOTES = '"';
	final public const CMD_PREAMBLE = '$';
	final public const CMD_BEGIN = '(';
	final public const CMD_ENDIN = ')';
	final public const ARG_SEPARATOR = ',';
	final public const VAL_SEPERATOR = '=';
	final public const ESCAPE_CHARACTER = '\\';

	private string $line;

	public function parse(string $line): GDT_Expression
	{
		$this->line = $line;
		$current = GDT_Expression::make();
		return $this->parseB($current->line($line), $this->line);
	}

	###############
	### Private ###
	###############
	private function parseB(GDT_Expression $current, string $line): GDT_Expression
	{
		global $me;
		$i = 0;
		$l = $line;
		$len = mb_strlen($l);
		$method = $me = $this->parseMethod($l, $i, $len);
		$current->method($method);
		$arg = '';
		while ($i < $len)
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
						case 'n':
						case 'N':
							$arg .= "\n";
							break;
						default:
							$arg .= $c2;
							break;
					}
					break;

				case self::ARG_SEPARATOR:
					$c2 = $l[$i];
					if ($c2 === self::ARG_SEPARATOR)
					{
						$arg .= $l[$i++];
					}
					elseif ($arg !== '')
					{
						$this->addArg($current, $arg);
					}
					break;

				default:
					$arg .= $c;
					break;
			}
		}
		if (trim($arg) !== '')
		{
			$this->addArg($current, $arg);
		}

		$current->applyInputs();

		$current->method->setupCLIButton();

		return $current;
	}

	private function parseMethod(string $line, int &$i, int $len): Method
	{
		$parsed = '';
		$started = false;
		for (; $i < $len;)
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
			elseif (ctype_alnum($c) || ($c === '_'))
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

		# Get method and button
		$button = null;
		if (substr_count($parsed, '.') === 2)
		{
			$button = Strings::rsubstrFrom($parsed, '.');
			$parsed = Strings::rsubstrTo($parsed, '.');
		}

		$method = Method::getMethod($parsed, true);

		if (!$method)
		{
			throw new GDO_NoSuchCommandError($parsed);
		}

//		if (!$button)
//		{
//			$button = $method->getAutoButton();
//		}

		if ($button)
		{
			$method->cliButton($button);
		}

		return $method;
	}

	/**
	 * Parse an additional line within parantheses.
	 */
	private function parseLine(string $line, int &$i, int $len): string
	{
		$parsed = '';
		for (; $i < $len;)
		{
			$c = $line[$i++];
			switch ($c)
			{
				case self::ESCAPE_CHARACTER:
					$c2 = $line[$i++];
					$parsed .= $c2;
					break;

				case self::CMD_ENDIN:
					break 2;

				default:
					$parsed .= $c;
					break;
			}
		}
		return $parsed;
	}

	private function addArgExpr(GDT_Expression $expression, GDT_Expression $arg): void
	{
		$expression->addInput(null, $arg->method);
	}

	private function addArg(GDT_Expression $expression, string &$arg): void
	{
		if (str_starts_with($arg, '--'))
		{
//			if ($expression->hasPositionalInput())
//			{
//				throw new GDO_Error('err_positional_after_named_parameter', [html($arg)]);
//			}
			$arg = substr($arg, 2);
// 			$arg = Strings::substrTo($arg, self::ARG_SEPARATOR, $arg);
			$key = Strings::substrTo($arg, self::VAL_SEPERATOR, $arg);
			$input = Strings::substrFrom($arg, self::VAL_SEPERATOR, '1');
		}
		else
		{
			$key = null;
			$input = $arg;
		}
		$arg = ''; # do not use EMPTY_STRING!, it's a ref
		$expression->addInput($key, $input);
	}

}
