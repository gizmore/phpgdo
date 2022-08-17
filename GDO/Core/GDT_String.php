<?php
namespace GDO\Core;

use GDO\UI\TextStyle;
use GDO\Util\Strings;

/**
 * A String is a database capable GDT_DBField, but you can also use it without a db.
 * 
 * - Optional min and max length
 * - Optional regex pattern
 * 
 * - Supports Binary,ASCII,UTF8
 * - Supports CaseI/S
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class GDT_String extends GDT_DBField
{
	/**
	 * Get the HTML <input> type.
	 * GDT_String template is re-used.
	 */
	public function getInputType() : string
	{
		return 'text';
	}
	
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

	public function isUTF8() : bool { return $this->encoding === self::UTF8; }
	public function isASCII() : bool { return $this->encoding === self::ASCII; }
	public function isBinary() : bool { return $this->encoding === self::BINARY; }
	
	#################
	### Min / Max ###
	#################
	public int $min = 0;
	public int $max = 192; # utf8mb4 max length for keys

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
	public function pattern(string $pattern=null) : self
	{
		if ($pattern === null)
		{
			unset($this->pattern);
		}
		else
		{
			$this->pattern = $pattern;
		}
		return $this;
	}
	
	public function noPattern() : self
	{
		unset($this->pattern);
		return $this;
	}
	
	public function validatePattern($value) : bool
	{
		if (!isset($this->pattern))
		{
			return true;
		}
		if (!preg_match($this->pattern, $value))
		{
			return $this->errorPattern();
		}
		return true;
	}
	
	public function htmlPattern() : string
	{
		if (isset($this->pattern))
		{
			$pattern = trim(rtrim($this->pattern, 'iuDs'), $this->pattern[0].'^$');
			return " pattern=\"{$pattern}\"";
		}
		return GDT::EMPTY_STRING;
	}
	
	protected function errorPattern() : bool
	{
		return $this->error('err_pattern_mismatch', [$this->pattern]);
	}
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		return $value === null ? true :  
			($this->validatePattern($value) &&
			$this->validateLength($value));
	}
	
	##########
	### DB ###
	##########
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
// 			default: throw new GDO_Error('err_invalid_string_encoding');
		}
	}
	
	protected function gdoCollateDefine(bool $caseSensitive) : string
	{
		if (!$this->isBinary())
		{
			$append = $caseSensitive ? '_bin' : '_general_ci';
			return ' COLLATE ' . $this->gdoCharsetDefine() . $append;
		}
		return GDT::EMPTY_STRING;
	}
	
	###########
	### GDT ###
	###########
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		$va = (string)$a->gdoVar($this->name);
		$vb = (string)$b->gdoVar($this->name);
		switch ($this->encoding)
		{
			case self::ASCII:
				return $this->caseSensitive ? strnatcmp($va, $vb) : strnatcasecmp($va, $vb);
			case self::UTF8:
				return Strings::compare($va, $vb, $this->caseSensitive);
			case self::BINARY:
				return strcmp($va, $vb);
		}
	}
	
	##############
	### Render ###
	##############
// 	public function renderCLI() : string
// 	{
// 		$out = '';
// 		if ($this->hasLabel())
// 		{
// 			$out .= $this->renderLabel() . ': ';
// 		}
// 		$out .= $this->getVar();
// 		return $out;
// 	}
	
// 	public function renderHTML() : string
// 	{
// 		$text = $this->renderCLI();
// 		return "<div>{$text}</div>";
// 	}
	
// 	public function renderCell() : string
// 	{
// 		return (string) $this->getVar();
// 	}
	
	public function renderForm() : string
	{
		return GDT_Template::php('Core', 'string_form.php', ['field' => $this]);
	}
	
	public function renderFilter($f) : string
	{
		return GDT_Template::php('Core', 'string_filter.php', ['field' => $this, 'f' => $f]);
	}
	
	public function displayVar(string $var=null) : string
	{
		return $var === null ? GDT::EMPTY_STRING : html($var);
	}
	
	public function plugVar() : string
	{
		return TextStyle::bold(
			TextStyle::italic(
				$this->getName()));
	}

}
