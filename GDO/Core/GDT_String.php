<?php
namespace GDO\Core;

/**
 * A string.
 * 
 * - optional length validator
 * - optional pattern validator
 * 
 * - Supports Binary,ASCII,UTF8
 * - Supports CaseI/S
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
class GDT_String extends GDT_DBField
{
	#######################
	### CaseSensitivity ###
	#######################
	public bool $caseSensitive = false;
	
	public function caseS(bool $caseSensitive = true) : self
	{
		$this->caseSensitive = $caseSensitive;
		return $this;
	}
	
	public function caseI() : self
	{
		return $this->caseS(false);
	}
	
	################
	### Encoding ###
	################
	const BINARY = 1;
	const ASCII = 2;
	const UTF8 = 3;
	public int $encoding = self::UTF8;
	public function encoding(int $encoding) : self
	{
		$this->encoding = $encoding;
		return $this;
	}
	public function utf8() : self { return $this->encoding(self::UTF8); }
	public function ascii() : self { return $this->encoding(self::ASCII); }
	public function binary() : self { return $this->encoding(self::BINARY); }

// 	public function isUTF8() : bool { return $this->encoding === self::UTF8; }
// 	public function isASCII() : bool { return $this->encoding === self::ASCII; }
	public function isBinary() : bool { return $this->encoding === self::BINARY; }
	
	#################
	### Min / Max ###
	#################
	public int $min = 0;
	public int $max = 192; # UTF8MB4 length

	public function min(int $min) : self
	{
		$this->min = $min;
		return $this;
	}
	
	public function max(int $max) : self
	{
		$this->max = $max;
		return $this;
	}
	
	public function validateLength($value)
	{
		$len = mb_strlen($value);
		if ($this->min > $len)
		{
			return $this->errorLength();
		}
		if ($this->max < $len)
		{
			return $this->errorLength();
		}
		return true;
	}
	
	protected function errorLength() : bool
	{
		return $this->error('err_string_length', [$this->min, $this->max]);
	}
	
	###############
	### Pattern ###
	###############
	public string $pattern;
	public function pattern(string $pattern) : self
	{
		$this->pattern = $pattern;
		return $this;
	}
	
	public function validatePattern($value) : bool
	{
		if (!$this->pattern)
		{
			return true;
		}
		if (!preg_match($this->pattern, $value))
		{
			return $this->errorPattern();
		}
	}
	
	protected function errorPattern() : bool
	{
		return $this->error('err_pattern_mismatch', [$this->pattern]);
	}
	
	#######################
	### Input/Var/Value ###
	#######################
	public function inputToVar(string $input) : string
	{
		if ($input)
		{
			return trim($input);
		}
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return
			parent::validate($value) &&
			$this->validatePattern($value) &&
			$this->validateLength($value);
	}
	
	##########
	### DB ###
	##########
// 	public function gdoColumnNames() : array
// 	{
// 		return [$this->name];
// 	}
	
	public function gdoColumnDefine() : string
	{
		$charset = $this->gdoCharsetDefine();
		$collate = $this->gdoCollateDefine($this->caseSensitive);
		$null = $this->gdoNullDefine();
		return "{$this->identifier()} VARCHAR({$this->max}) CHARSET {$charset}{$collate}{$null}";
	}
	
	protected function gdoCharsetDefine() : string
	{
		switch ($this->encoding)
		{
			case self::UTF8: return 'utf8mb4';
			case self::ASCII: return 'ascii';
			case self::BINARY: return 'binary';
			default: throw new GDO_Error('err_invalid_string_encoding');
		}
	}
	
	protected function gdoCollateDefine(bool $caseSensitive) : string
	{
		if (!$this->isBinary())
		{
			$append = $caseSensitive ? '_bin' : '_general_ci';
			return ' COLLATE ' . $this->gdoCharsetDefine() . $append;
		}
		return '';
	}
	
}
