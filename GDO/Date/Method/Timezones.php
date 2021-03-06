<?php
namespace GDO\Date\Method;

use GDO\Core\MethodAjax;
use GDO\Core\GDT_JSON;
use GDO\Date\GDO_Timezone;

/**
 * Get all timezones and offsets via ajax.
 * 
 * @deprecated unneded?
 * @author gizmore
 * @version 7.0.0
 * @since 6.10.6
 */
final class Timezones extends MethodAjax
{
	public function getMethodTitle() : string
	{
		return t('mt_timezones');
	}
	
	public function getMethodDescription() : string
	{
		return t('md_timezones');
	}
	
	public function execute()
	{
		$data = GDO_Timezone::table()->allTimezones();
		return GDT_JSON::make()->value($data);
	}

}
