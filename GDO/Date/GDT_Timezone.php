<?php
namespace GDO\Date;

use GDO\Core\GDT_ObjectSelect;

/**
 * Timezone select.
 * inputToVar() does convert +NNNN to the first timezone matching the offset.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.10.0
 */
final class GDT_Timezone extends GDT_ObjectSelect
{
    public function getDefaultName() : string { return 'timezone'; }
    public function defaultLabel() : self { return $this->label('gdo_timezone'); }
    
    public function isSearchable() : bool { return false; }
    
    protected function __construct()
    {
        parent::__construct();
        $this->notNull();
        $this->table = GDO_Timezone::table();
        $this->initial('1');
        $this->icon('time');
        $this->completionHref(href('Date', 'TimezoneComplete'));
    }
    
    public function inputToVar($input=null) : ?string
    {
    	if ($input !== null)
    	{
	    	$input = trim($input);
	    	if (preg_match('#^[-+]?\\d{3,4}$#D', $input))
	    	{
	    		$input = $this->getBestTimezoneIdForOffset($input);
	    	}
    	}
    	return $input;
    }
    
    public function plugVars() : array
    {
    	return [
    		GDO_Timezone::getBy('tz_name', 'UTC')->getID(),
    		GDO_Timezone::getBy('tz_name', 'Europe/Berlin')->getID(),
    	];
    }
    
    ###############
    ### Private ###
    ###############
    private function getBestTimezoneIdForOffset(int $offset) : string
    {
    	return '2';
    }

}
