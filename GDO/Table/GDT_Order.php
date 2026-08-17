<?php
declare(strict_types=1);
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\WithFields;
use GDO\DB\Query;
use GDO\UI\GDT_Icon;
use GDO\UI\WithHREF;
use GDO\Util\Strings;

/**
 * A orderby parameter.
 * Validates if the GDO has a column.
 * Generates href cycle for template.
 *
 * @version 7.0.3
 * @author gizmore
 */
final class GDT_Order extends GDT_String
{

	use WithHREF;
	use WithFields;

	# current ordering state for a GDT.
	final public const ASC = 0;
	final public const DESC = 1;
	final public const NONE = 2;

	############
	###  GDT ###
	############

	public string $icon = 'arrow_up';

	public array $extraFields;
	/**
	 * @var GDT[]
	 */
	public array $orders;

	####################
	### Extra Fields ###
	####################

	public function isTestable(): bool { return false; }

	public function gdtDefaultName(): ?string { return 'order'; }

	################
	### Validate ###
	################

	public function validate(int|float|string|array|null|object|bool $value): bool
	{
		if ($value)
		{
			if (is_string($value))
			{
				$value = Strings::explode($value);
			}
			foreach ($value as $order)
			{
				if (!$this->validateOrder(trim($order)))
				{
					return false;
				}
			}
		}
		return true;
	}

	private function validateOrder(string $order): bool
	{
//		$dir = Strings::substrFrom($order, ' ', 'ASC');
//		$dir = stripos($dir, 'DESC') === false ? 'ASC' : 'DESC';
		$by = Strings::substrTo($order, ' ', $order);
		$by = Strings::rsubstrFrom($by, '.', $by);
		if (isset($this->extraFields) && in_array($by, $this->extraFields, true))
		{
			return true;
		}
		if (!($gdt = $this->getField($by, false)))
		{
			return $this->error('err_unknown_order_column', [html($by)]);
		}
		if (!$gdt->isOrderable())
		{
			return $this->error('err_non_order_column', [$gdt->gdoHumanName()]);
		}
		return true;
	}

	public function plugVars(): array
	{
		return [];
	}

	#############
	### Order ###
	#############

	public function extraFields(array $fields): self
	{
		$this->extraFields = $fields;
		return $this;
	}

	public function orders(array $orders): self
	{
		$this->orders = $orders;
		return $this;
	}

	###########
	### Var ###
	###########

	public function getOrderBy(): ?string
	{
		$by = $this->getOrderBys();
		return $by[0] ?: null;
	}

	private function getOrderBys(): array
	{
		$o = $this->getVar();
		$os = explode(',', $o);
		return array_map(function ($o)
		{
			return explode(' ', $o)[0];
		}, $os);
	}

	public function getVar(): string|array|null
	{
		$name = $this->name;
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}
		if (isset($this->inputs["{$name}_by"]))
		{
			return $this->inputs["{$name}_by"] . ' ' . $this->inputs["{$name}_dir"];
		}
		return $this->var;
	}

	#############
	### Query ###
	#############

	public function orderQuery(Query $query): self
	{
		$query->order($this->getVar());
		return $this;
	}

	##############
	### Render ###
	##############
	/**
	 * Generate the next href for a GDT.
	 */
	public function nextHref(GDT $gdt): string
	{
		$name = $gdt->getName();
		$asc = $gdt->isDefaultAsc();
		switch ($this->state($gdt))
		{
			case self::ASC:
				$replace = $asc ? "{$name} DESC" : '';
				break;
			case self::DESC:
				$replace = $asc ? '' : "{$name} ASC";
				break;
			default:
				$replace = $asc ? "{$name} ASC" : "{$name} DESC";
				break;
		}
		return $this->hrefReplaced($gdt, $replace);
	}

	/**
	 * Calculate ordering state.
	 */
	private function state(GDT $gdt): int
	{
		$name = $gdt->getName();
		if (isset($this->orders[$name]))
		{
			return $this->orders[$name] ? self::ASC : self::DESC;
		}
		return self::NONE;
	}

	/**
	 * Generate a new href from the desired replacement for the GDT.
	 *
	 * Keep every other order in its current precedence. This is shared by
	 * regular GDT_Table instances and MethodTable, so both table types expose
	 * the same ASC -> DESC -> none cycle and support multi-column ordering.
	 */
	private function hrefReplaced(GDT $gdt, string $replace): string
	{
		$name = $gdt->getName();
		$orders = [];
		$replaced = false;
		foreach ($this->orderParts() as $order)
		{
			if ($this->orderColumn($order) === $name)
			{
				if ($replace)
				{
					$orders[] = $replace;
				}
				$replaced = true;
			}
			else
			{
				$orders[] = $order;
			}
		}
		if (!$replaced && $replace)
		{
			$orders[] = $replace;
		}
		return $this->hrefWithOrder(implode(',', $orders));
	}

	/** @return string[] */
	private function orderParts(): array
	{
		$value = $this->getVar();
		$value = is_array($value) ? implode(',', $value) : $value;
		return array_values(array_filter(array_map('trim', explode(',', (string)$value))));
	}

	private function orderColumn(string $order): string
	{
		$column = Strings::substrTo($order, ' ', $order);
		return Strings::rsubstrFrom($column, '.', $column);
	}

	/**
	 * Replace this order parameter in a regular query string. An underscore
	 * keeps it out of the SEO path and lets all table variants share this URL
	 * contract.
	 */
	private function hrefWithOrder(string $order): string
	{
		[$href, $fragment] = array_pad(explode('#', $this->href, 2), 2, null);
		[$path, $query] = array_pad(explode('?', $href, 2), 2, null);
		$query = $query === null ? [] : explode('&', $query);
		$query = array_values(array_filter($query, function (string $part): bool
		{
			return urldecode(Strings::substrTo($part, '=', $part)) !== $this->name;
		}));
		$query[] = $this->name . '=' . urlencode($order);
		$href = $path . '?' . implode('&', $query);
		return $fragment === null ? $href : "{$href}#{$fragment}";
	}

	###############
	### Private ###
	###############

	public function htmlOrderClass(GDT $gdt): string
	{
		switch ($this->state($gdt))
		{
			case self::ASC:
			case self::DESC:
				return 'selected';
			default:
				return GDT::EMPTY_STRING;
		}
	}

	public function htmlOrderIcon(GDT $gdt): string
	{
		if ($icon = $this->getOrderIcon($gdt))
		{
			return GDT_Icon::iconS($icon);
		}
		return GDT::EMPTY_STRING;
	}

	private function getOrderIcon(GDT $gdt): string
	{
		switch ($this->state($gdt))
		{
			case self::ASC:
				return 'arrow_up';
			case self::DESC:
				return 'arrow_down';
			default:
				return $this->icon;
		}
	}

}
