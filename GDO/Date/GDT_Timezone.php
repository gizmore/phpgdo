<?php
namespace GDO\Date;

use GDO\Core\GDT_ObjectSelect;

/**
 * Timezone select.
 * 
 * @author gizmore
 * @version 6.11.2
 * @since 6.10.0
 */
final class GDT_Timezone extends GDT_ObjectSelect
{
    public function defaultName() { return 'timezone'; }
    public function defaultLabel() : self { return $this->label('timezone'); }
    
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
    
    public function plugVar() : string
    {
        return GDO_Timezone::getBy('tz_name', 'Europe/Berlin')->getID();
    }

}
