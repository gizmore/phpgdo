<?php
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
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Order extends GDT_String
{

	use WithHREF;
	use WithFields;

	# current ordering state for a GDT.
	public const ASC = 0;
	public const DESC = 1;
	public const NONE = 2;

	############
	###  GDT ###
	############
	public array $extraFields;
	/**
	 * @var GDT[]
	 */
	public array $orders;

	####################
	### Extra Fields ###
	####################

	public function isTestable(): bool { return false; }

	public function getDefaultName(): string { return 'order'; }

	################
	### Validate ###
	################

	public function validate($value): bool
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
		$dir = Strings::substrFrom($order, ' ', 'ASC');
		$dir = stripos($dir, 'desc') === false ? 'ASC' : 'DESC';
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
		return @$this->getOrderBys()[0];
	}

	public function getOrderBys(): array
	{
		$o = $this->getVar();
		$os = explode(',', $o);
		return array_map(function ($o)
		{
			return explode(' ', $o)[0];
		}, $os);
	}

	public function getVar()
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

	public function getOrderDir(): ?string
	{
		return @$this->getOrderDirs()[0];
	}

	public function getOrderDirs(): array
	{
		$o = $this->getVar();
		$os = explode(',', $o);
		return array_map(function ($o)
		{
			return Strings::substrFrom($o, ' ', self::ASC);
		}, $os);
	}

	#############
	### Query ###
	#############
// 	public function filterQuery(Query $query, GDT_Filter $f): self
// 	{
// 		$query->order($this->getVar());
// 		return $this;
// 	}

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
				$replace = $asc ? "{$name}%20DESC" : '';
				break;
			case self::DESC:
				$replace = $asc ? '' : "{$name}%20ASC";
				break;
			default:
				$replace = $asc ? "{$name}%20ASC" : "{$name}%20DESC";
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
		if (null !== ($state = @$this->orders[$name]))
		{
			return $state ? self::ASC : self::DESC;
		}
		else
		{
			return self::NONE;
		}
	}

	/**
	 * Generate a new href from the desigered replacement for the GDT.
	 */
	private function hrefReplaced(GDT $gdt, string $replace): string
	{
		$name = $gdt->getName();
		$href = preg_replace("#,? *{$name} *(?:DESC|ASC)#", '', urldecode($this->href));
		$matches = null;
		$old = '';
		$con = strpos($href, '?') ? '&' : '?';
		if (preg_match("#(&\\?)({$this->name}=[^&]*)#", $href, $matches))
		{
			$con = $matches[1];
			$old = $matches[2];
		}
		$href = str_replace($old, '', $href);
		$href .= $old ? "{$con}{$old}" : "{$con}{$this->name}=";
		if ($replace)
		{
			$href .= ",{$replace}";
		}
		$href = str_replace('=,', '=', $href);
		return $href;
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
				return GDT::EMPTY_STRING;
		}
	}

}
