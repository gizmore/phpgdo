<?php
namespace GDO\Table;

use GDO\Core\GDT_UInt;

/**
 * Items per page for headers.
 * Defaults to Module_Table->cfgIPP() (cli and http variants exist)
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
final class GDT_IPP extends GDT_UInt
{
	#############
	### Field ###
	#############
    public function defaultLabel() : self
    {
    	return $this->label('ipp');
    }

    ################
    ### Features ###
    ################
    public function isOrderable() : bool { return false; }
    public function isSearchable() : bool { return false; }
    public function isFilterable() : bool { return false; }
    public function isSerializable() : bool { return false; }
    
	###########
    ### GDT ###
    ###########
    protected function __construct()
    {
        parent::__construct();
        $this->initial(Module_Table::instance()->cfgItemsPerPage());
        $this->min = 1;
        $this->max = 1000;
        $this->bytes = 2;
    }
    
}
