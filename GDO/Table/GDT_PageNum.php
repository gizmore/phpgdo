<?php
namespace GDO\Table;

use GDO\Core\GDT_UInt;

/**
 * Page num to select.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
final class GDT_PageNum extends GDT_UInt
{
    public int $bytes = 2;
    public ?string $initial = '1';
    public bool $hidden = true;

    public function isOrderable() : bool { return false; }
    public function isSearchable() : bool { return false; }
    public function isFilterable() : bool { return false; }
    public function isSerializable() : bool { return false; }
    
    public function getDefaultName() : string { return 'page'; }
    public function defaultLabel() : self { return $this->label('page'); }

    #############
    ### Table ###
    #############
    public $table;
    public function table(GDT_Table $table)
    {
        $this->table = $table;
        return $this;
    }
    
    #############
    ### Query ###
    #############
//     public function filterQuery(Query $query, $rq=null) : self
//     {
//     	$ipp = $this->table->getPageMenu()->ipp;
//     	$page = $this->table->getPageMenu()->getPage();
//     	$query->limit($ipp, ($page - 1) * $ipp);
// //     	$filter = $this->filterVar($rq);
// //     	if ($filter != '')
// //     	{
// //     		if ($condition = $this->searchQuery($query, $filter, true))
// //     		{
// //     			$this->filterQueryCondition($query, $condition);
// //     		}
// //     	}
//     	return $this;
//     }
    

    ###############
    ### Example ###
    ###############
    public function gdoExampleVars() : ?string
    {
        $this->min = 1;
        $this->max = $this->table->getPageMenu()->getPageCount();
        return parent::gdoExampleVars();
    }
    
    public function plugVars() : array
    {
    	$name = $this->getName();
    	return [
    		[$name => '1'],
    		[$name => '2'],
    	];
    }

    ################
    ### Validate ###
    ################
//     public function validate($value) : bool
//     {
//         $this->min = 1;
//         $this->max = $this->table->getPageMenu()->getPageCount();
//         return parent::validate($value);
//     }
    
}

