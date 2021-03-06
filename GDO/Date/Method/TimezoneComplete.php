<?php
namespace GDO\Date\Method;

use GDO\Core\GDT_JSON;
use GDO\Core\MethodCompletion;
use GDO\Date\GDO_Timezone;
use GDO\UI\GDT_SearchField;

/**
 * Timezone autocompletion.
 * 
 * @author gizmore
 * @version 6.11.0
 * @since 6.10.5
 */
final class TimezoneComplete extends MethodCompletion
{
	public function isUserRequired() : bool { return false; }
	
	public function getMethodTitle() : string
	{
		return t('gdo_timezone');
	}
	
	public function getMethodDescription() : string
	{
		return t('gdo_timezone');
	}
	
	public function gdoParameters() : array
	{
		return [
// 			GDT_Country::make('country'),
			GDT_SearchField::make('query')->notNull(),
		];
	}
	
	public function execute()
    {
//     	$country = $this->gdoParameterVar('country');
    	$timezones = GDO_Timezone::table()->select()->exec();
    	$q = $this->getSearchTerm();
        $json = [];
        while ($timezone = $timezones->fetchObject())
        {
        	$tz = $timezone->getName();
            if (stripos($tz, $q) !== false)
            {
                $json[] = [
                    'id' => $timezone->getID(),
                    'text' => $tz,
                    'display' => $tz . ' ' . $timezone->displayOffset(),
                ];
            }
        }
        return GDT_JSON::make()->value($json);
    }
    
}
