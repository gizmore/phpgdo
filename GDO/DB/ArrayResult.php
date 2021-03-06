<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT;

/**
 * Mimics a GDO Result from database.
 * Used in, e.g. Admin_Modules overview, as its loaded from FS.
 * 
 * @author gizmore
 * @version 6.10.3
 * @since 5.0.0
 */
final class ArrayResult extends Result
{
    /**
     * @var GDO[]
     */
    private array $data;
    
    /**
	 * @var GDO[]
	 */
	private array $fullData;
	
	/**
	 * @var int
	 */
	private int $index = -1;
	
	public function __construct(array &$data, GDO $table)
	{
		$this->data = &$data;
		$this->fullData = &$data;
		$this->table = $table;
		$this->reset();
	}
	
	public function data(array &$data) : self
	{
	    $this->data = &$data;
	    return $this;
	}
	
	public function fullData(array &$fullData) : self
	{
	    $this->fullData = &$fullData;
	    return $this;
	}
	
	public function &getData() : array
	{
	    return $this->data;
	}
	
	public function &getFullData() : array
	{
	    return $this->fullData;
	}
	
	#############
	### Table ###
	#############
	public function reset() : self { $this->index = 0; return $this; }
	public function numRows() :int { return count($this->data); }
	public function fetchRow() : array { return array_values($this->fetchAssoc()); }
	public function fetchAssoc() : array { return $this->fetchObject()->getGDOVars(); }
	public function fetchAs(GDO $table) : ?GDO { return $this->fetchObject(); }
	/**
	 * @return GDO
	 */
	public function fetchObject() : ?GDO
	{
	    if ($this->index >= count($this->data))
	    {
	        return null;
	    }
	    $slice = array_slice($this->data, $this->index++, 1);
	    return array_pop($slice);
	}
	
	public function fetchInto(GDO $gdo) : ?GDO
	{
	    if ($o = $this->fetchObject())
	    {
	        return $gdo->setGDOVars($o->getGDOVars());
	    }
	    return null;
	}
	
	
	##############
	### Filter ###
	##############
	/**
	 *
	 * @param GDO[] $data
	 * @param GDO $table
	 * @param GDT[] $filters
	 * @return ArrayResult
	 */
	public function filterResult(array $data, GDO $table, array $filters, $rq) : self
	{
	    foreach ($filters as $gdt)
	    {
	        if ($gdt->isFilterable())
	        {
	            $filter = $gdt->filterVar($rq);
	            if ($filter !== null)
	            {
	                $keep = [];
	                foreach ($data as $gdo)
	                {
	                    if ($gdt->gdo($gdo)->filterGDO($gdo, $filter))
    	                {
    	                    $keep[] = $gdo;
    	                }
	                }
	                $data = $keep;
	            }
	        }
	    }
	    $this->data = $data;
	    return $this;
	}

	##############
	### Search ###
	##############
	/**
	 * Deepsearch a static result. Like a global table search.
	 * @param GDO[] $data
	 * @param GDO   $table
	 * @param GDT[] $filters
	 */
	public function searchResult(array $data, GDO $table, array $filters, string $searchTerm) : self
	{
	    if ($searchTerm !== null)
	    {
	        $hits = [];
            foreach ($data as $gdo)
            {
        	    foreach ($filters as $gdt)
        	    {
        	        if ($gdt->isSearchable())
        	        {
       	                if ($gdt->gdo($gdo)->searchGDO($searchTerm))
       	                {
       	                    $hits[] = $gdo;
       	                    break;
        	            }
        	        }
        	    }
            }
            $data = $hits;
	    }
	    
	    $this->data = $data;
	    
	    return $this;
	}
	
}
