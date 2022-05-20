<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT_Join;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_ObjectSelect;
use GDO\Core\Logger;
use GDO\Core\GDO_Error;

/**
 * GDO Query Builder.
 * Part of the GDO DBAL code.
 * You should use GDO Classes to create queries.
 * 
 * @example GDO_User::table()->select()->execute()->fetchAll();
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 * @see GDO
 * @see Cache
 * @see Result
 * @see Database
 */
final class Query
{
	# Type constants
	const SELECT = "SELECT";
	const INSERT = "INSERT INTO";
	const REPLACE = "REPLACE INTO";
	const UPDATE = "UPDATE";
	const DELETE = "DELETE FROM";
	
	/**
	 * The table in FROM/INTO
	 */
	public GDO $table;
	
	/**
	 * The fetch into object gdo table / final class.
	 */
	public GDO $fetchTable;
	
	# query parts
	private string $columns;
	private string $where;
	private string $join;
	private string $group;
	private string $having;
	private string $from;
	private string $type;
	private string $set;
	public  array  $order;
	public  array  $values;
	private string $limit;
	private string $raw;
	private bool $write = false; # Is it a write query?
	private bool $cached = true;
	public  bool $buffered = true;
	public  bool $debug = false;
	
	public function __construct(GDO $table)
	{
		$this->table = $table;
		$this->fetchTable = $table;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * Use this to avoid using the GDO cache. This means the memcache might be still used? This means no single identity?
	 * @return \GDO\DB\Query
	 */
	public function uncached() : self { return $this->cached(false); }
	public function cached(bool $cached=true) : self { $this->cached = $cached; return $this; }

	/**
	 * Mark this query's buffered mode.
	 * @param boolean $buffered
	 * @return self
	 */
	public function buffered(bool $buffered) : self
	{
	    $this->buffered = $buffered;
	    return $this;
	}
	
	public function unbuffered() : self
	{
	    return $this->buffered(false);
	}
	
	#############
	### Debug ###
	#############
	/**
	 * Enable logging and verbose output.
	 * @return \GDO\DB\Query
	 */
	public function debug($debug=true) : self { $this->debug = $debug; return $this; }
	
	#############
	### Clone ###
	#############
	/**
	 * Copy this query.
	 * @return self
	 */
	public function copy() : self
	{
		$clone = new self($this->table);
		if ($this->raw)
		{
		    $clone->raw = $this->raw;
		}
		else
		{
            $clone->fetchTable = $this->fetchTable;
    		$clone->type = $this->type;
    		$clone->columns = $this->columns;
    		$clone->from = $this->from;
    		$clone->where = $this->where;
    		$clone->join = $this->join;
    		$clone->joinedObjects = $this->joinedObjects;
    		$clone->group = $this->group;
    		$clone->having = $this->having;
            $clone->order = $this->order;
            $clone->limit = $this->limit;
    		$clone->from = $this->from;
    		$clone->write = $this->write;
    		$clone->debug = $this->debug;
    		$clone->cached = $this->cached;
    		return $clone;
		}
	}
	
	/**
	 * Specify which GDO class is used for fetching.
	 * @TODO Rename function
	 * @param GDO $fetchTable
	 * @return \GDO\DB\Query
	 */
	public function fetchTable(GDO $fetchTable) : self
	{
		$this->fetchTable = $fetchTable;
		return $this;
	}
	
	public function update(string $tableName) : self
	{
		$this->type = self::UPDATE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	public function insert(string $tableName) : self
	{
		$this->type = self::INSERT;
		$this->write = true;
		return $this->from($tableName);
	}
	
	public function replace(string $tableName) : self
	{
		$this->type = self::REPLACE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	/**
	 * @param string $condition
	 * @param string $op
	 * @return static
	 */
	public function where(string $condition, string $op="AND") : self
	{
		$this->where = isset($this->where) ? $this->where . " $op ($condition)" : "($condition)";
		return $this;
	}
	
	public function orWhere($condition) : self
	{
		return $this->where($condition, "OR");
	}
	
	public function getWhere() : string
	{
		return isset($this->where) ? " WHERE {$this->where}" : "";
	}
	
	/**
	 * @param string $condition
	 * @param string $op
	 * @return self
	 */
	public function having(string $condition, string $op="AND") : self
	{
		if (isset($this->having))
		{
			$this->having .= " $op ($condition)";
		}
		else
		{
			$this->having= "($condition)";
		}
		return $this;
	}
	
	public function getHaving() : string
	{
		return isset($this->having) ? " HAVING {$this->having}" : "";
	}
	
		
	/**
	 * @param string $tableName
	 * @return self
	 */	
	public function from(string $tableName) : self
	{
		$this->from = isset($this->from) ? $this->from . ", $tableName" : $tableName;
		return $this;
	}
	
	public function fromSelf() : self
	{
		return $this->from($this->table->gdoTableIdentifier());
	}
	
	public function getFrom() : string
	{
		return isset($this->from) ? " {$this->from}" : "";
	}
	
	/**
	 * Build a select.
	 * @param string $columns
	 * @return self
	 */
	public function select(string $columns=null) : self
	{
		$this->type = self::SELECT;
		if ($columns) # ignore empty
		{
			$this->columns = isset($this->columns) ? 
				"{$this->columns}, $columns" :
				" $columns";
		}
		return $this;
	}
	
	/**
	 * Select a field as first column in query.
	 * Useful to build count queries out of filtered tables etc.
	 * @param string $columns
	 * @return self
	 */
	public function selectAtFirst(string $columns="COUNT(*)") : self
	{
	    if ($columns)
	    {
	        $this->columns = isset($this->columns) ? 
	           " {$columns}, {$this->columns}" : " $columns";
	    }
	    return $this;
	}
	
	/**
	 * Continue to build a select but reset columns.
	 * This may be useful in pagination queries.
	 * 
	 * @param string $columns
	 * @return self
	 */
	public function selectOnly(string $columns=null) : self
	{
		unset($this->columns);
	    return $this->select($columns);
	}
	
	/**
	 * @param int $count
	 * @param int $start
	 * @return self
	 */
	public function limit(int $count, int $start=0) : self
	{
		$this->limit = " LIMIT {$start}, {$count}";
		return $this;
	}
	
	public function noLimit() : self
	{
	    unset($this->limit);
	    return $this;
	}
	
	/**
	 * Limit results to one.
	 * @return self
	 */
	public function first() : self
	{
		return $this->limit(1);
	}
		
	public function getLimit() : string
	{
		return isset($this->limit) ? $this->limit : '';
	}
	
	public function getSelect() : string
	{
		return $this->write ? '' : ($this->getSelectColumns() . ' FROM');
	}
	
	private function getSelectColumns() : string
	{
		return isset($this->columns) ? $this->columns : ' *';
	}
	
	public function delete(string $tableName) : self
	{
		$this->type = self::DELETE;
		$this->write = true;
		return $this->from($tableName);
	}
	
	/**
	 * Build part of the SET clause.
	 * @param string $set
	 * @return self
	 */
	public function set(string $set) : self
	{
		if (isset($this->set))
		{
			$this->set .= ',' . $set;
		}
		else
		{
			$this->set = $set;
		}
		return $this;
	}
	
	public function getSet() : string
	{
		return isset($this->set) ? " SET {$this->set}" : "";
	}

	
	public function noOrder() : self
	{
	    unset($this->order);
	    return $this;
	}
	
	/**
	 * Order clause.
	 * @param string $order
	 * @return self
	 */
	public function order(string $order) : self
	{
	    if (!isset($this->order))
	    {
	        $this->order = [$order];
	    }
	    else
	    {
	        $this->order[] = $order;
	    }
		return $this;
	}
	
	public function join(string $join) : self
	{
		if (isset($this->join))
		{
			$this->join .= " $join";
		}
		else
		{
			$this->join = " $join";
		}
		return $this;
	}
	
	/**
	 * 
	 * @var array
	 */
	private array $joinedObjects = [];
	
	/**
	 * Automatically build a join based on a GDT_Object column of this queries GDO table.
	 * 
	 * @see GDO
	 * @param string $key the GDT_Object
	 * @param string $join type
	 * @return Query
	 */
	public function joinObject(string $key, string $join='JOIN', string $tableAlias=null) : self
	{
		if (in_array($key, $this->joinedObjects, true))
		{
			return $this;
		}
		
		$this->joinedObjects[] = $key;
		
		if (!($gdt = $this->table->gdoColumn($key)))
		{
			throw new GDO_Error(t('err_column', [html($key)]));
		}
		
		if ($gdt instanceof GDT_Join)
		{
			$join = $gdt->join;
		}
		elseif ( ($gdt instanceof GDT_Object) ||
			($gdt instanceof GDT_ObjectSelect) )
		{
			$table = $gdt->table;
			$ftbl = $tableAlias ? $tableAlias : $table->gdoTableIdentifier();
			$atbl = $this->table->gdoTableIdentifier();
			$tableAlias = $tableAlias ? " AS {$tableAlias}" : '';
			
			$join = "{$join} {$table->gdoTableIdentifier()}{$tableAlias} ON {$ftbl}.{$table->gdoPrimaryKeyColumn()->identifier()}=$atbl.{$gdt->identifier()}";
		}
		else
		{
			throw new GDO_Error(t('err_join_object', [html($key), html($this->table->displayName())]));
		}
		
		return $this->join($join);
	}
	
	public function group(string $group) : self
	{
		$this->group = isset($this->group) ? "{$this->group},{$group}" : $group;
		return $this;
	}
	
	public function values(array $values) : self
	{
	    $this->values = isset($this->values) ? array_merge($this->values, $values) : $values;
		return $this;
	}
	
	public function getValues() : string
	{
		if (!isset($this->values))
		{
			return '';
		}
		$fields = [];
		$values = [];
		foreach ($this->values as $key => $value)
		{
			$fields[] = $key;
			$values[] = GDO::quoteS($value);
		}
		$fields = implode(',', $fields);
		$values = implode(',', $values);
		return " ($fields) VALUES ($values)";
	}
	
	public function getJoin() : string
	{
		return isset($this->join) ? " {$this->join}" : "";
	}
	
	public function noJoins() : self
	{
	    $this->join = null;
	    return $this;
	}
	
	public function getGroup() : string
	{
		return isset($this->group) ? " GROUP BY $this->group" : "";
	}
	
	public function getOrderBy() : string
	{
		return isset($this->order) ? ' ORDER BY ' . implode(', ', $this->order) : '';
	}
	
	public function raw(string $raw) : self
	{ 
	    $this->write = !str_starts_with($raw, 'SELECT');
	    $this->raw = $raw;
	    return $this;
	}

	/**
	 * Build the query string.
	 * @return string
	 */
	public function buildQuery() : string
	{
	    return isset($this->raw) ?
    	    $this->raw :
    	    $this->type .
    	    $this->getSelect() .
    	    $this->getFrom() .
    	    $this->getValues() .
    	    $this->getJoin() .
    	    $this->getSet() .
    	    $this->getWhere() .
    	    $this->getGroup() .
    	    $this->getHaving() .
    	    $this->getOrderBy() .
    	    $this->getLimit();
	}
	
	/**
	 * Execute a query.
	 * Returns boolean on writes and a Result on reads.
	 * @see Result
	 * @return Result
	 */
	public function exec()
	{
		$db = Database::instance();

		$query = $this->buildQuery();

		if ($this->debug)
		{
			echo "{$query}\n";
			Logger::rawLog('query', $query);
		}
		
		if ($this->write)
		{
			return $db->queryWrite($query);
		}
		else
		{
			return new Result($this->fetchTable, $db->queryRead($query, $this->buffered), $this->cached);
		}
	}
	
}
