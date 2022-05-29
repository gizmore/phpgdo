<?php
namespace GDO\Table;

use GDO\Core\GDT_UInt;

/**
 * Items per page for headers.
 * 
 * @author gizmore
 *
 */
final class GDT_PageNum extends GDT_UInt
{
    public bool $hidden = true;
    public bool $orderable = false;
    public bool $searchable = false;
    public bool $filterable = false;
    
    public int $bytes = 2;
    public ?string $initial = '1';
    
    public function getDefaultName() : string { return 'page'; }
    public function defaultLabel() : self { return $this->label('page'); }

    public function isSerializable() : bool{ return false; }
    
    #############
    ### Table ###
    #############
    public $table;
    public function table(GDT_Table $table)
    {
        $this->table = $table;
        return $this;
    }

    ###############
    ### Example ###
    ###############
    public function gdoExampleVars()
    {
        $this->min = 1;
        $this->max = $this->table->getPageMenu()->getPageCount();
        return parent::gdoExampleVars();
    }

    ################
    ### Validate ###
    ################
    public function validate($value) : bool
    {
        $this->min = 1;
        $this->max = $this->table->getPageMenu()->getPageCount();
        return parent::validate($value);
    }
    
}

