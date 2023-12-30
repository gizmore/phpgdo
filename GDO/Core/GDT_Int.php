<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\Util\Arrays;
use GDO\Util\WS;

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
 * @version 7.0.3
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


	public int|float $step = 1;

	public bool $unsigned = false;

	############
	### Step ###
	############
	public null|int|float $min = null;

	public null|int|float $max = null;

	################
	### Unsigned ###
	################

	public function toVar(null|bool|int|float|string|object|array $value): ?string
	{
		return $value === null ? null : (string)$value;
	}

	/**
	 * @throws GDO_Exception
	 * @throws GDO_Exception
	 * @throws GDO_DBException
	 */
	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if (parent::validate($value))
		{
			if ($value !== null)
			{
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

	public function toValue(null|string|array $var): null|bool|int|float|string|object|array
	{
		return $var === null ? null : intval($var);
	}

	###############
	### Min/Max ###
	###############

	/**
	 * Appropiate min / max validation.
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
		return false;
	}

	protected function validateUnique($value): bool
	{
		if ($this->unique)
		{
			$condition = "{$this->getName()}=" . GDO::quoteS($value);

			# @TODO: polymorph fix for int gdo uniques.
			if ($this->gdo->isPersisted()) // persisted
			{ // ignore own row
				$condition .= ' AND NOT ( ' . $this->gdo->getPKWhere() . ' )';
			}
			return $this->gdo->tbl()->select(GDT::ONE)->where($condition)->
				first()->exec()->fetchVar() !== GDT::ONE;
		}
		return true;
	}

	public function plugVars(): array
	{
		$max4 = min(4, $this->max ?: 4);
		return [
			[$this->getName() => (string) $max4],
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

	public function renderBinary(): string
	{
		return WS::wrN($this->bytes, (int)$this->getVar());
	}


	public function htmlClass(): string
	{
		return ' gdt-num ' . parent::htmlClass();
	}

	public function renderForm(): string
	{
		return GDT_Template::php('Core', 'integer_form.php', ['field' => $this]);
	}

	public function renderHTML(): string
	{
		return GDT_Float::displayS($this->getVar(), 0);
	}

	public function renderJSON(): array|string|null|int|bool|float
	{
		return $this->getVar();
	}

	public function configJSON(): array
	{
		return array_merge(parent::configJSON(), [
			'min' => $this->min,
			'max' => $this->max,
			'bytes' => $this->bytes,
			'signed' => !$this->unsigned,
		]);
	}

	public function renderFilter(GDT_Filter $f): string
	{
		return GDT_Template::php('Core', 'integer_filter.php', [
			'field' => $this, 'f' => $f]);
	}

	##############
	### Render ###
	##############

	/**
	 * @throws GDO_Exception
	 */
	public function filterQuery(Query $query, GDT_Filter $f): static
	{
		if (null !== ($filter = $this->filterVar($f)))
		{
			$this->searchQuery($query, $filter);
//			if ($condition = )
//			{
//				$this->filterQueryCondition($query, $condition);
//			}
		}
		return $this;
	}

    public function filterVar(GDT_Filter $f): null|string|array
    {
        if ( ($flt = $f->getVar()) && ($name = $this->getName()) )
        {
            if (isset($flt[$name]))
            {
                return Arrays::empty($flt[$name]) ? null : self::intFilterVar($flt[$name]);
//                $fv = trim($flt[$name]);
//                return $fv === '' ? null : $fv;
            }
        }
        return null;
    }

//    public function filterVar(GDT_Filter $f): null|string|array
//	{
////		$fv = parent::filterVar($f);
//		return Arrays::empty($fv) ? null : self::intFilterVar($fv);
//	}

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

	/**
	 * @throws GDO_Exception
	 */
	public function searchQuery(Query $query, string $searchTerm): static
	{
		if ($this->isSearchable())
		{
			$searchTerm = GDO::quoteS($searchTerm);
			$query->orWhere("{$this->name} = {$searchTerm}");
		}
		return $this;
	}

	/**
	 * @throws GDO_ExceptionFatal
	 */
	public function filterGDO(GDO $gdo, $filterInput): bool
	{
		$min = $filterInput['min'];
		$max = $filterInput['max'];
		$var = $gdo->gdoValue($this->name);
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
        $n = $this->getName();
        $va = $a->gdoValue($n);
        $vb = $b->gdoValue($n);
        return $va === $vb ? 0 :
            ($va > $vb ? 1 : -1);
	}

	public function bytes(int $bytes): self
	{
		$this->bytes = $bytes;
		return $this;
	}

	public function step(int|float $step): self
	{
		$this->step = $step;
		return $this;
	}

	public function unsigned(bool $unsigned = true): self
	{
		$this->unsigned = $unsigned;
		return $this;
	}

	public function min(null|int|float $min): self
	{
		$this->min = $min;
		return $this;
	}

	public function max(null|int|float $max): self
	{
		$this->max = $max;
		return $this;
	}

}
