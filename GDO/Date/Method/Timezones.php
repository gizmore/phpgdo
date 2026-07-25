<?php
namespace GDO\Date\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_JSON;
use GDO\Core\MethodAjax;
use GDO\Date\GDO_Timezone;

/**
 * Get all timezones and offsets via ajax.
 *
 * @version 7.0.0
 * @since 6.10.6
 * @author gizmore
 */
final class Timezones extends MethodAjax
{

	public function getMethodTitle(): string
	{
		return t('mt_timezones');
	}

	public function getMethodDescription(): string
	{
		return t('md_timezones');
	}

	public function execute(): GDT
	{
		$data = GDO_Timezone::table()->allCached('tz_name', true);
		return GDT_JSON::make()->value($data);
	}

}
