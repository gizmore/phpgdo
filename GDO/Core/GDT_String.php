<?php
namespace GDO\Core;

use GDO\Util\Strings;
use GDO\Table\GDT_Filter;
use GDO\Form\GDT_Hidden;

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
	
	public function caseS(bool $caseSensitive = true): static
	{
		$this->caseSensitive = $caseSensitive;
		return $this;
	}
	
	public function caseI(): static
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
	public function encoding(int $encoding): static
	{
		$this->encoding = $encoding;
		return $this;
	}
	public function utf8(): static { return $this->encoding(self::UTF8); }
	public function ascii(): static { return $this->encoding(self::ASCII); }
	public function binary(): static { return $this->encoding(self::BINARY); }

// 	public function isUTF8() : bool { return $this->encoding === self::UTF8; }
// 	public function isASCII() : bool { return $this->encoding === self::ASCII; }
	public function isBinary() : bool { return $this->encoding === self::BINARY; }
	
	#################
	### Min / Max ###
	#################
	public int $min = 0;
	public int $max = 191; # utf8mb4 max length for keys

	public function min(int $min): static
	{
		$this->min = $min;
		return $this;
	}
	
	public function max(int $max): static
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
		if ($this->max < $this->min)
		{
			return $this->error('err_min_max_confusion');
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
	public function pattern(string $pattern=null): static
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
	
	public function noPattern(): static
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
			($this->validateUnique($value) &&
			$this->validatePattern($value) &&
			$this->validateLength($value));
	}
	
	protected function validateUnique($value) : bool
	{
		if (isset($this->gdo) && $this->unique)
		{
			$condition = "{$this->identifier()}=".quote($value);
			if ($this->gdo->isPersisted())
			{
				# ignore own row
				$condition .= " AND NOT ( " . $this->gdo->getPKWhere() . " )";
			}
			if ($this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() === '1')
			{
				return $this->error('err_db_unique');
			}
		}
		return true;
	}
	
	###########
	### GDT ###
	###########
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		$va = (string) $a->gdoVar($this->name);
		$vb = (string) $b->gdoVar($this->name);
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
	public function configJSON() : array
	{
		return array_merge(parent::configJSON(), [
			'min' => $this->min,
			'max' => $this->max,
		]);
	}
	
	public function renderForm() : string
	{
		if ($this->isHidden())
		{
			return $this->renderFormHidden();
		}
		return GDT_Template::php('Core', 'string_form.php', ['field' => $this]);
	}
	
	public function renderFormHidden() : string
	{
		$hidden = GDT_Hidden::make($this->getName())->var($this->getVar());
		return $hidden->renderForm();
	}
	
// 	public function renderList() : string
// 	{
// 		$text = $this->renderLabelText();
// 		if ($text)
// 		{
// 			$text .= ':&nbsp;';
// 		}
// 		$text .= $this->displayVar($this->getVar());
// 		return "<div class=\"gdt-li-string\">$text</div>";
// 	}
	
	public function renderFilter(GDT_Filter $f) : string
	{
		return GDT_Template::php('Core', 'string_filter.php', ['field' => $this, 'f' => $f]);
	}
	
// 	public function displayVar(string $var=null) : string
// 	{
// 		return $var === null ? GDT::EMPTY_STRING : html($var);
// 	}
	
	public function plugVars() : array
	{
		$str = 'str<i>ng</i>s';
		$str = mb_substr($str, 0, $this->max);
		return [
			[$this->getName() => $str],
		];
	}

}
