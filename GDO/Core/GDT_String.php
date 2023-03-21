<?php
namespace GDO\Core;

use GDO\Form\GDT_Hidden;
use GDO\Table\GDT_Filter;
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
 * @version 7.0.1
 * @since 5.0.0
 * @author gizmore
 */
class GDT_String extends GDT_DBField
{

	public const BINARY = 1;

	#######################
	### CaseSensitivity ###
	#######################
	public const ASCII = 2;
	public const UTF8 = 3;
	public bool $caseSensitive = false;

	################
	### Encoding ###
	################
	public int $encoding = self::UTF8;
	public int $min = 0;
	public int $max = 191;
	public string $pattern;

	/**
	 * Get the HTML <input> type.
	 * GDT_String template is re-used.
	 */
	public function getInputType(): string
	{
		return 'text';
	}

	public function caseI(): self
	{
		return $this->caseS(false);
	}

	public function caseS(bool $caseSensitive = true): self
	{
		$this->caseSensitive = $caseSensitive;
		return $this;
	}

	public function utf8(): self { return $this->encoding(self::UTF8); }

// 	public function isUTF8() : bool { return $this->encoding === self::UTF8; }
// 	public function isASCII() : bool { return $this->encoding === self::ASCII; }

	public function encoding(int $encoding): self
	{
		$this->encoding = $encoding;
		return $this;
	}

	#################
	### Min / Max ###
	#################

	public function ascii(): self { return $this->encoding(self::ASCII); }

	public function binary(): self { return $this->encoding(self::BINARY); } # utf8mb4 max length for keys

	public function isBinary(): bool { return $this->encoding === self::BINARY; }

	public function min(int $min): self
	{
		$this->min = $min;
		return $this;
	}

	public function max(int $max): self
	{
		$this->max = $max;
		return $this;
	}

	public function pattern(string $pattern = null): self
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

	###############
	### Pattern ###
	###############

	public function noPattern(): self
	{
		unset($this->pattern);
		return $this;
	}

	public function htmlPattern(): string
	{
		if (isset($this->pattern))
		{
			$pattern = trim(rtrim($this->pattern, 'iuDs'), $this->pattern[0] . '^$');
			return " pattern=\"{$pattern}\"";
		}
		return GDT::EMPTY_STRING;
	}

	public function validate($value): bool
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

	protected function validateUnique($value): bool
	{
		if (isset($this->gdo) && $this->unique)
		{
			$condition = "{$this->identifier()}=" . quote($value);
			if ($this->gdo->isPersisted())
			{
				# ignore own row
				$condition .= ' AND NOT ( ' . $this->gdo->getPKWhere() . ' )';
			}
			if ($this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() === '1')
			{
				return $this->error('err_db_unique');
			}
		}
		return true;
	}

	public function validatePattern($value): bool
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

	protected function errorPattern(): bool
	{
		return $this->error('err_pattern_mismatch', [$this->pattern]);
	}

	################
	### Validate ###
	################

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

	protected function errorLength(): bool
	{
		return $this->error('err_string_length', [$this->min, $this->max]);
	}

	###########
	### GDT ###
	###########

	public function gdoCompare(GDO $a, GDO $b): int
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
	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'min' => $this->min,
			'max' => $this->max,
		]);
	}

	public function renderForm(): string
	{
		if ($this->isHidden())
		{
			return $this->renderFormHidden();
		}
		return GDT_Template::php('Core', 'string_form.php', ['field' => $this]);
	}

	public function renderFormHidden(): string
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

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'string_filter.php', ['field' => $this, 'f' => $f]);
	}

// 	public function displayVar(string $var=null) : string
// 	{
// 		return $var === null ? GDT::EMPTY_STRING : html($var);
// 	}

	public function plugVars(): array
	{
		$str = 'str<i>ng</i>s';
		$str = mb_substr($str, 0, $this->max);
		return [
			[$this->getName() => $str],
		];
	}

}
