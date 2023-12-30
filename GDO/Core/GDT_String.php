<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Country\GDT_Country;
use GDO\DB\Query;
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
 * @version 7.0.3
 * @since 5.0.0
 * @author gizmore
 */
class GDT_String extends GDT_DBField
{

	final public const BINARY = 1;
	final public const ASCII = 2;
	final public const UTF8 = 3;

	public string $icon = 'text';

	#######################
	### CaseSensitivity ###
	#######################

	public bool $caseSensitive = false;


	################
	### Encoding ###
	################

	public int $encoding = self::UTF8;
	public ?int $min = 0;
	public ?int $max = 191;
	public string $pattern;

	/**
	 * Get the HTML <input> type.
	 * GDT_String template is re-used.
	 */
	public function getInputType(): string
	{
		return 'text';
	}

	public function caseI(): static
	{
		return $this->caseS(false);
	}

	public function caseS(bool $caseSensitive = true): static
	{
		$this->caseSensitive = $caseSensitive;
		return $this;
	}

	public function utf8(): static { return $this->encoding(self::UTF8); }

 	public function isUTF8() : bool { return $this->encoding === self::UTF8; }

 	public function isASCII() : bool { return $this->encoding === self::ASCII; }

	public function encoding(int $encoding): static
	{
		$this->encoding = $encoding;
		return $this;
	}

	#################
	### Min / Max ###
	#################

	public function ascii(): static { return $this->encoding(self::ASCII); }

	public function binary(): static { return $this->encoding(self::BINARY); } # utf8mb4 max length for keys

	public function isBinary(): bool { return $this->encoding === self::BINARY; }

	public function min(?int $min): static
	{
		$this->min = $min;
		return $this;
	}

	public function max(?int $max): static
	{
		$this->max = $max;
		return $this;
	}

	public function pattern(?string $pattern): static
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

	public function noPattern(): static
	{
		return $this->pattern(null);
	}

	public function htmlPattern(): string
	{
		if (isset($this->pattern))
		{
			$pattern = html(trim(rtrim($this->pattern, 'iuDs'), $this->pattern[0] . '^$'));
			return " pattern=\"{$pattern}\"";
		}
		return GDT::EMPTY_STRING;
	}

	/**
	 * @throws GDO_DBException
	 * @throws GDO_ExceptionFatal
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (!parent::validate($value))
		{
			return false;
		}
		if ($value === null)
		{
			return true;
		}
		$value = (string) $value;
		return $this->validateUnique($value) &&
			$this->validatePattern($value) &&
			$this->validateLength($value);
	}

	/**
	 * @throws GDO_DBException
	 * @throws GDO_ExceptionFatal
	 */
	protected function validateUnique($value): bool
	{
		if (isset($this->gdo) && $this->unique)
		{
			$condition = "{$this->getName()}=" . quote($value);
			if ($this->gdo->isPersisted())
			{
				# ignore own row
				$condition .= ' AND NOT ( ' . $this->gdo->getPKWhere() . ' )';
			}
			if ($this->gdo->tbl()->select(GDT::ONE)->where($condition)->first()->exec()->fetchVar() === GDT::ONE)
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

	public function validateLength($value): bool
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
			default:
				return 0;
//			default:
//				throw new GDO_ExceptionFatal('err_invalid_gdo_encoding', [$this->encoding]);
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

	public function renderBinary(): string
	{
		$binary = $this->getVar();
		$binary = is_string($binary) ? urlencode($binary) : GDT::EMPTY_STRING;
		return $binary . "\0";
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
		return [
			[$this->getName() => mb_substr($str, 0, $this->max)],
		];
	}

	##############
	### Search ###
	##############
	public function searchQuery(Query $query, string $searchTerm): static
	{
		if ($this->isSearchable())
		{
			$search = GDO::escapeSearchS($searchTerm);
			$query->orWhere("BINARY {$this->name} LIKE BINARY '%{$search}%'");
		}
		return $this;
	}

}
