<?php
namespace GDO\Date\Method;

use GDO\Core\MethodAjax;
use GDO\Core\GDT_Array;
use GDO\Date\GDO_Timezone;
use GDO\Core\GDT;

/**
 * Get all timezones and offsets via ajax.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.6
 */
final class Timezones extends MethodAjax
{
	public function execute() : GDT
	{
		$data = GDO_Timezone::table()->allTimezones();
		return GDT_Array::makeWith($data);
	}

}
