<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\DB\Query;
use GDO\Util\Strings;
use GDO\UI\WithHREF;
use GDO\UI\GDT_Icon;
use GDO\Core\WithFields;
use GDO\Core\GDT_String;

/**
 * A orderby parameter.
 * Validates if the GDO has a column.
 * Generates href cycle for template.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class GDT_Order extends GDT_String
{
	use WithHREF;
	use WithFields;
	
	# current ordering state for a GDT.
	const ASC = 0; 
	const DESC = 1;
	const NONE = 2;
	
	############
	###  GDT ###
	############
	public function isTestable() : bool { return false; }
	
	public function getDefaultName() : string { return 'order'; }

	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		if ($value)
		{
			$orders = explode(',', $value);
			foreach ($orders as $order)
			{
				if (!$this->validateOrder(trim($order)))
				{
					return false;
				}
			}
		}
		return true;
	}
	
	private function validateOrder(string $order) : bool
	{
		$dir = Strings::substrFrom($order, ' ', 'ASC');
		$dir = stripos($dir, 'desc') === false ? 'ASC' : 'DESC';
		$by = Strings::substrTo($order, ' ', $order);
		$by = Strings::rsubstrFrom($by, '.', $by);
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
	
	public function plugVars() : array
	{
		return [];
	}
	
	#############
	### Order ###
	#############
	public array $orders;
	public function orders(array $orders) : self
	{
		$this->orders = $orders;
		return $this;
	}
	
	#############
	### Query ###
	#############
	public function filterQuery(Query $query, $rq='') : self
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
	public function nextHref(GDT $gdt) : string
	{
		$name = $gdt->getName();
		$asc = $gdt->isDefaultAsc();
		switch ($this->state($gdt))
		{
			case self::ASC:
				$replace = $asc ? "{$name}%20DESC" : "";
				break;
			case self::DESC:
				$replace = $asc ? "" : "{$name}%20ASC";
				break;
			default:
				$replace = $asc ? "{$name}%20ASC" : "{$name}%20DESC";
				break;
		}
		return $this->hrefReplaced($gdt, $replace);
	}
	
	public function htmlOrderClass(GDT $gdt) : string
	{
		switch ($this->state($gdt))
		{
			case self::ASC:
			case self::DESC:
				return 'selected';
			default:
				return '';
		}
	}

	public function htmlOrderIcon(GDT $gdt) : string
	{
		if ($icon = $this->getOrderIcon($gdt))
		{
			return GDT_Icon::iconS($icon);
		}
		return '';
	}
	
	###############
	### Private ###
	###############
	/**
	 * Calculate ordering state.
	 */
	private function state(GDT $gdt) : int
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
	private function hrefReplaced(GDT $gdt, string $replace) : string
	{
		$name = $gdt->getName();
		$href = preg_replace("#,? *{$name} *(?:DESC|ASC)#", '', urldecode($this->href));
		$matches = [];
		$old = '';
		if (preg_match("#(&{$this->name}=[^&]*)#", $href, $matches))
		{
			$old = $matches[1];
		}
		$href = str_replace($old, '', $href);
		$href .= $old ? $old : "&{$this->name}=";
		if ($replace)
		{
			$href .= ",{$replace}";
		}
		$href = str_replace('=,', '=', $href);
		return $href; 
	}
	
	private function getOrderIcon(GDT $gdt) : string
	{
		switch ($this->state($gdt))
		{
			case self::ASC:
				return 'arrow_up';
			case self::DESC:
				return 'arrow_down';
			default:
				return '';
		}
	}
	
}
