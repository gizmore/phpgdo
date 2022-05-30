<?php
namespace GDO\Table;

use GDO\Core\GDT;
use GDO\Core\WithGDO;
use GDO\DB\Query;
use GDO\Core\WithValue;

final class GDT_Order extends GDT
{
	use WithGDO;
// 	use WithValue;
	
	public function getDefaultName() : string { return 'order'; }
	
	public function filterQuery(Query $query, $rq='') : GDT
	{
		$query->order($this->getVar());
		return $this;
	}
	
}
