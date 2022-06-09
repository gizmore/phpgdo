<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\WithGDO;
use GDO\DB\Query;
use GDO\Core\GDT_String;
use GDO\Util\Strings;

/**
 * A orderby parameter.
 * Validates if the GDO has a column.
 * 
 * @author gizmore
 * @version 7.0.0
 */
final class GDT_Order extends GDT_String
{
	use WithGDO;
	
	public function getDefaultName() : string { return 'order'; }
	
	public function filterQuery(Query $query, $rq='') : GDT
	{
		$query->order($this->getVar());
		return $this;
	}
	
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
		if (!$this->gdo->hasColumn($by))
		{
			return $this->error('err_unknown_gdo_column', [$this->gdo->gdoHumanName(), html($by)]);
		}
		return true;
	}
	
	public function plugVars() : array
	{
		return [];
	}
	
}
