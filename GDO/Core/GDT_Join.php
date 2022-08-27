<?php
namespace GDO\Core;

use GDO\DB\Query;

/**
 * Can be used with $query->joinObject('col_name') to add a predefined join to a query.
 * 
 * @author gizmore
 * @see GDT_Object
 * @version 7.0.1
 * @since 6.0.1
 */
final class GDT_Join extends GDT
{
	use WithName;
	
	public function isTestable() : bool { return false; }
	
	public function isSearchable() : bool { return true; }
	
// 	public static function make(string $name = null) : self
// 	{
// 		return self::makeNamed($name);
// 	}
	
	############
	### Join ###
	############

	public GDO $table;
	public string $as;
	public string $join;
	public function join(GDO $table, string $as, string $on, string $type='LEFT')
	{
	    $this->as = $as;
	    $this->table = $table;
		$this->join = "{$type} JOIN {$table->gdoTableIdentifier()} AS {$as} ON {$on}";
		return $this;
	}
	
	public function joinRaw($join, $type='LEFT')
	{
		unset($this->table);
	    $this->join = "{$type} JOIN $join";
	    return $this;
	}
	
	###################
	### Render stub ###
	###################
	public function searchQuery(Query $query, $searchTerm, $first)
	{
	    if ($this->table)
	    {
	        $conditions = [];
	        foreach ($this->table->gdoColumnsCache() as $gdt)
	        {
	            if ($gdt->searchable)
	            {
	                $conditions[] = $gdt->searchCondition($searchTerm, $this->as);
	            }
	        }
	        return implode(' OR ', $conditions);
	    }
	}
	
}
