<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\Util\Arrays;

/**
 * Database capable base integer class.
 * Is also is the base class for GDT_Objects
 *
 * Control ->bytes(4) for size.
 * Control ->unsigned([true]) for unsigned.
 * Control ->min() and ->max() for validation.
 * Control ->step() for html5 fancy.
 *
 * Is inherited by GDT_Object for auto_inc relation.
 * Can validate uniqueness.
 * Can compare gdo instances.
 * Is searchable and orderable.
 * Uses WithLabel, WithFormFields, WithDatabase and WithOrder.
 *
 * @version 7.0.1
 * @since 6.0.0
 * @author gizmore
 * @see GDT_UInt
 * @see GDT_Decimal
 * @see GDT_Float
 * @see GDT_Object
 */
class GDT_Int extends GDT_DBField
{

	public string $icon = 'numeric';
	public int $bytes = 4;

	#############
	### Bytes ###
	#############
	public float $step = 1.0;
	public bool $unsigned = false;

	############
	### Step ###
	############
	public ?float $min = null;
	public ?float $max = null;

	################
	### Unsigned ###
	################

	public function toValue($var = null)
	{
		return $var === null ? null : intval($var, 10);
	}

	public function validate($value): bool
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
// 				if (!$this->is_numeric($this->getRequestVar()))
// 				{
// 					return $this->numericError();
// 				}
				if (($this->max !== null) && ($this->max < $this->min))
				{
					return $this->error('err_min_max_confusion');
				}
				if (
					(($this->min !== null) && ($value < $this->min)) ||
					(($this->max !== null) && ($value > $this->max))
				)
				{
					return $this->intError();
				}
				if (!$this->validateUnique($value))
				{
					return $this->error('err_db_unique');
				}
			}
			return true;
		}
		return false;
	}

	###############
	### Min/Max ###
	###############

	/**
	 * Appropiate min / max validation.
	 *
	 * @return bool
	 */
	private function intError(): bool
	{
		if (($this->min !== null) && ($this->max !== null))
		{
			return $this->error('err_int_not_between', [$this->min, $this->max]);
		}
		if ($this->min !== null)
		{
			return $this->error('err_int_too_small', [$this->min]);
		}
		if ($this->max !== null)
		{
			return $this->error('err_int_too_large', [$this->max]);
		}
	}

	protected function validateUnique($value): bool
	{
		if ($this->unique)
		{
			$condition = "{$this->identifier()}=" . GDO::quoteS($value);
			if ($this->gdo->isPersisted()) // persisted
			{ // ignore own row
				$condition .= ' AND NOT ( ' . $this->gdo->getPKWhere() . ' )';
			}
			return $this->gdo->table()->select('1')->where($condition)->first()->exec()->fetchValue() !== '1';
		}
		return true;
	}

	public function plugVars(): array
	{
		return [
			[$this->getName() => '4'],
		];
	}

	public function gdoExampleVars(): ?string
	{
		if (($this->min !== null) && ($this->max !== null))
		{
			if ($this->min === $this->max)
			{
				return $this->min;
			}
			else
			{
				return $this->min . '-' . $this->max;
			}
		}
		if ($this->max !== null)
		{
			return '-∞-' . $this->max;
		}
		if ($this->min !== null)
		{
			return $this->min . '-∞';
		}
		return t('number');
	}

	################
	### Validate ###
	################
// 	public function is_numeric(string $input) : bool
// 	{
// 		return !!Regex::firstMatch('/^([0-9][-+\\d\\.,]*)$/iD', $input);
// 	}

	public function htmlClass(): string
	{
		return sprintf(' gdt-num %s', parent::htmlClass());
	}

	public function renderForm(): string
	{
		return GDT_Template::php('Core', 'integer_form.php', ['field' => $this]);
	}

	public function renderHTML(): string
	{
		return GDT_Float::displayS($this->getVar(), 0);
	}

	public function renderJSON()
	{
		return $this->getValue();
	}

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'bytes' => $this->bytes,
			'signed' => true,
			'min' => $this->min,
			'max' => $this->max,
		]);
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'integer_filter.php', ['field' => $this, 'f' => $f]);
	}

	##############
	### Render ###
	##############

	public function filterQuery(Query $query, GDT_Filter $f): self
	{
		if ($filter = $this->filterVar($f))
		{
			if ($condition = $this->searchQuery($query, $filter, true))
			{
				$this->filterQueryCondition($query, $condition);
			}
		}
		return $this;
	}

	public function filterVar(GDT_Filter $f): ?string
	{
		$fv = parent::filterVar($f);
		return Arrays::empty($fv) ? null : self::intFilterVar($fv);
	}

	private function intFilterVar(array $fv): array
	{
		foreach ($fv as $k => $v)
		{
			if ($v !== null)
			{
				$v = trim($v);
				$v = $v === '' ? null : (int)$v;
			}
			$fv[$k] = $v;
		}
		return $fv;
	}

	public function searchQuery(Query $query, string $term): self
	{
		$term = GDO::escapeS($term);
		$query->orWhere("{$this->name} = '{$term}'");
		return $this;
//	    return $this->searchCondition($searchTerm);
	}

	public function filterGDO(GDO $gdo, $filtervalue): bool
	{
		$min = $filtervalue['min'];
		$max = $filtervalue['max'];
		$var = $this->getVar();
		if (($min !== null) && ($var < $min))
		{
			return false;
		}
		if (($max !== null) && ($var > $max))
		{
			return false;
		}
		return true;
	}

	##############
	### Filter ###
	##############

	/**
	 * Comparing two integers is not that hard.
	 */
	public function gdoCompare(GDO $a, GDO $b): int
	{
		return
			$a->gdoValue($this->name) -
			$b->gdoValue($this->name);
	}

	public function bytes(int $bytes): self
	{
		$this->bytes = $bytes;
		return $this;
	}

	public function step(float $step): self
	{
		$this->step = $step;
		return $this;
	}

	public function unsigned(bool $unsigned = true): self
	{
		$this->unsigned = $unsigned;
		return $this;
	}

	public function min(float $min): self
	{
		$this->min = $min;
		return $this;
	}

	public function max(float $max): self
	{
		$this->max = $max;
		return $this;
	}

	##############
	### Search ###
	##############

	private function numericError(): bool
	{
		return $this->error('err_input_not_numeric');
	}

// 	public function searchGDO($searchTerm)
// 	{
// 	    $haystack = (string) $this->getVar();
// 	    return strpos($haystack, $searchTerm) !== false;
// 	}

//	public function searchCondition($searchTerm) : string
//	{
//		$searchTerm = (int)$searchTerm;
//		return "{$this->name} = {$searchTerm}";
//	}

}
