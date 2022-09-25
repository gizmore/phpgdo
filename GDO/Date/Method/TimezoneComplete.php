<?php
namespace GDO\Date\Method;

use GDO\Core\GDO;
use GDO\Core\MethodCompletion;
use GDO\Date\GDO_Timezone;

/**
 * Timezone autocompletion.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.5
 */
final class TimezoneComplete extends MethodCompletion
{
	protected function gdoTable(): GDO
	{
		return GDO_Timezone::table();
	}
	
	public function isUserRequired() : bool
	{
		return false;
	}
	
	public function getMethodTitle() : string
	{
		return t('gdo_timezone');
	}
	
	public function getMethodDescription() : string
	{
		return t('gdo_timezone');
	}
	
	public function itemToCompletionJSON(GDO $item) : array
    {
    	$tz = $item->renderName();
    	return [
	    	'id' => $item->getID(),
	    	'text' => $tz,
	    	'display' => $tz . ' ' . $item->displayOffset(),
    	];
    }

}
