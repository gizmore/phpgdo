<?php
declare(strict_types=1);
namespace GDO\DB;

use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT;
use GDO\Core\GDT_Join;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_ObjectSelect;
use GDO\Core\Logger;

/**
 * GDO Query Builder.
 * Is it SQL compliant? I have no clue :)
 * Part of the GDO DBAL code.
 * You should use GDO Classes to create queries.
 *
 * @version 7.0.3
 * @since 5.0.0
 * @example GDO_User::table()->select()->execute()->fetchAll();
 *
 * @author gizmore
 * @see GDO
 * @see Cache
 * @see Result
 * @see Database
 */
final class Query
{

	# Type constants
	final public const RAW = 0;

	final public const SELECT = 1;

	final public const INSERT = 2;

	final public const REPLACE = 3;

	final public const UPDATE = 4;

	final public const DELETE = 5;

	final public const INSERT_OR_UPDATE = 6;

	/**
	 * The fetch into object gdo table / final class.
	 */
	public GDO $table;
	public GDO $fetchTable;

	# query parts
	public ?array $order = null;
	public ?array $values = null;
	public ?array $nonPKValues = null;
	public bool $buffered = true;
	public bool $debug = false;
	public ?self $union = null;
	private ?string $columns = null;
	private ?string $where = null;
	private ?string $join = null;
	private ?string $group = null;
	private ?string $having = null;
	private ?string $from = null;
	private int $type = self::RAW;
	private ?string $set = null; # Is it a write query?
	private ?string $limit = null;
	private ?string $raw = null;
	private bool $write = false;
	private bool $cached = true;

	private array $joinedObjects = [];

	#############
	### Cache ###
	#############

	public function __construct(GDO $table)
	{
		$this->table = $table;
		$this->fetchTable = $table;
	}

	/**
	 * Use this to avoid using the GDO cache.
	 * This means the memcache might be still used? This means no single identity?
	 */
	public function uncached(): self { return $this->cached(false); }

	public function cached(bool $cached = true): self
	{
		$this->cached = $cached;
		return $this;
	}

	public function unbuffered(): self
	{
		return $this->buffered(false);
	}

	#############
	### Debug ###
	#############

	/**
	 * Mark this query's buffered mode.
	 */
	public function buffered(bool $buffered): self
	{
		$this->buffered = $buffered;
		return $this;
	}

	#############
	### Clone ###
	#############

	/**
	 * Enable logging and verbose output.
	 */
	public function debug($debug = true): self
	{
		$this->debug = $debug;
		return $this;
	}

	/**
	 * Copy this query.
	 */
	public function copy(): self
	{
		$clone = new self($this->table);
		if (isset($this->raw))
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
			$clone->write = $this->write;
			$clone->cached = $this->cached;
		}
		return $clone;
	}

	/**
	 * Specify which GDO class is used for fetching. @TODO Rename function.
	 */
	public function fetchTable(GDO $fetchTable): self
	{
		$this->fetchTable = $fetchTable;
		return $this;
	}

	public function update(string $tableName): self
	{
		$this->type = self::UPDATE;
		$this->write = true;
		return $this->from($tableName);
	}

	public function from(string $tableName): self
	{
		$this->from = isset($this->from) ? $this->from . ", $tableName" : $tableName;
		return $this;
	}

	public function insert(string $tableName): self
	{
		$this->type = self::INSERT;
		$this->write = true;
		return $this->from($tableName);
	}

	public function softReplace(string $tableName): self
	{
		$this->type = self::INSERT_OR_UPDATE;
		$this->write = true;
		return $this->from($tableName);
	}

	public function replace(string $tableName): self
	{
		$this->type = self::REPLACE;
		$this->write = true;
		return $this->from($tableName);
	}

	public function orWhere($condition): self
	{
		return $this->where($condition, 'OR');
	}

	public function where(string $condition, string $op = 'AND'): self
	{
		$this->where = isset($this->where) ? $this->where . " $op ($condition)" : "($condition)";
		return $this;
	}

	public function having(string $condition, string $op = 'AND'): self
	{
		if (isset($this->having))
		{
			$this->having .= " $op ($condition)";
		}
		else
		{
			$this->having = "($condition)";
		}
		return $this;
	}

	public function fromSelf(): self
	{
		return $this->from($this->table->gdoTableIdentifier());
	}

	/**
	 * Select a field as first column in query.
	 * Useful to build count queries out of filtered tables etc.
	 */
	public function selectAtFirst(string $columns = 'COUNT(*)'): self
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
	 */
	public function selectOnly(string $columns = null): self
	{
		unset($this->columns);
		return $this->select($columns);
	}

	/**
	 * Build a select.
	 */
	public function select(string $columns = null): self
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

	public function noLimit(): self
	{
		unset($this->limit);
		return $this;
	}

	/**
	 * Limit results to one.
	 */
	public function first(): self
	{
		return $this->limit(1);
	}

	public function limit(int $count, int $start = 0): self
	{
		$this->limit = " LIMIT {$start}, {$count}";
		return $this;
	}

	public function delete(string $tableName): self
	{
		$this->type = self::DELETE;
		$this->write = true;
		return $this->onlyFrom($tableName);
	}

	public function onlyFrom(string $tableName): self
	{
		$this->from = $tableName;
		return $this;
	}

	/**
	 * Build part of the SET clause.
	 */
	public function set(string $set): self
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

	public function noOrder(): self
	{
		unset($this->order);
		return $this;
	}

	public function orderRand(): self
	{
		try
		{
			return $this->order(Database::DBMS()->dbmsRandom());
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return $this;
		}
	}

	/**
	 * Order clause.
	 */
	public function order(string $order = null): self
	{
		if ($order)
		{
			if (!isset($this->order))
			{
				$this->order = [$order];
			}
			else
			{
				$this->order[] = $order;
			}
		}
		return $this;
	}

	/**
	 * Automatically build a join based on a GDT_Object column of this queries GDO table.
	 */
	public function joinObject(string $key, string $join = 'JOIN'): self
	{
		if (in_array($key, $this->joinedObjects, true))
		{
			return $this;
		}

		$this->joinedObjects[] = $key;

		$gdt = $this->table->gdoColumn($key);

		if ($gdt instanceof GDT_Join)
		{
			$join = $gdt->join;
		}
		elseif (
			($gdt instanceof GDT_Object) ||
			($gdt instanceof GDT_ObjectSelect)
		)
		{
			$table = $gdt->table;
			$atbl = $this->table->gdoTableIdentifier();
			$ftbl = "{$key}_t";
			$tableAlias = " AS {$ftbl}";
			$join = "{$join} {$table->gdoTableIdentifier()}{$tableAlias} ON {$ftbl}.{$table->gdoPrimaryKeyColumn()->getName()}=$atbl.{$gdt->getName()}";
		}
//		else
//		{
//			throw new GDO_DBException('err_join_object', [html($key), html($this->table->renderName())]);
//		}
		return $this->join($join);
	}

	public function join(string $join): self
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

	public function group(string $group): self
	{
		$this->group = isset($this->group) ? "{$this->group},{$group}" : $group;
		return $this;
	}

	public function updateValues(array $values): self
	{
		$this->nonPKValues = isset($this->nonPKValues) ? array_merge($this->nonPKValues, $values) : $values;
		return $this->values($values);
	}

	public function values(array $values): self
	{
		$this->values = isset($this->values) ? array_merge($this->values, $values) : $values;
		return $this;
	}

	public function noJoins(): self
	{
		$this->join = null;
		return $this;
	}

	public function raw(string $raw): self
	{
		$raw = trim($raw);
		$this->write = !str_starts_with($raw, 'SELECT');
		$this->raw = $raw;
		return $this;
	}

	public function union(self $query): self
	{
		$this->union = $query;
		return $this;
	}

	public function getUnion(): ?string
	{
		return $this->union ?
			(' UNION ' . $this->union->buildQuery()) :
			null;
	}

	/**
	 * Build the query string.
	 */
	public function buildQuery(): string
	{
		return $this->raw ??
			$this->getType() .
			$this->getSelect() .
			$this->getFrom() .
			$this->getValues() .
			$this->getJoin() .
			$this->getSet() .
			$this->getWhere() .
			$this->getGroup() .
			$this->getHaving() .
			$this->getOrderBy() .
			$this->getLimit() .
			$this->getUnion();
	}

	public function getType(): string
	{
		switch ($this->type)
		{
			case self::RAW:
				return GDT::EMPTY_STRING;
			case self::SELECT:
				return 'SELECT ';
			case self::INSERT:
			case self::INSERT_OR_UPDATE:
				return 'INSERT INTO ';
			case self::REPLACE:
				return 'REPLACE INTO ';
			case self::UPDATE:
				return 'UPDATE ';
			case self::DELETE:
//				return "DELETE FROM ";
				return "DELETE FROM {$this->from}";
			default:
				return "!INVALID!QRY!TYPE!{$this->type}";
		}
	}

	public function getSelect(): string
	{
		return $this->write ? '' : ($this->getSelectColumns() . ' FROM');
	}

	private function getSelectColumns(): string
	{
		return $this->columns ?? ' *';
	}

	public function getFrom(): string
	{
		return isset($this->from) ? " {$this->from}" : '';
	}

	public function getValues(): string
	{
		if (!isset($this->values))
		{
			return GDT::EMPTY_STRING;
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
		$rawsql = " ($fields) VALUES ($values)";

		if ($this->type === self::INSERT_OR_UPDATE)
		{
			$dupsql = '';
			foreach ($this->nonPKValues as $key => $var)
			{
				$dupsql .= ",{$key}=" . quote($var);
			}
			$rawsql .= ' ON DUPLICATE KEY UPDATE ';
			$rawsql .= trim($dupsql, ' ,');
		}

		return $rawsql;
	}

	public function getJoin(): string
	{
		return isset($this->join) ? " {$this->join}" : '';
	}

	public function getSet(): string
	{
		return isset($this->set) ? " SET {$this->set}" : '';
	}

	public function getWhere(): string
	{
		return isset($this->where) ? " WHERE {$this->where}" : '';
	}

	#############
	### Union ###
	#############

	public function getGroup(): string
	{
		return isset($this->group) ? " GROUP BY $this->group" : '';
	}

	public function getHaving(): string
	{
		return isset($this->having) ? " HAVING {$this->having}" : '';
	}

	public function getOrderBy(): string
	{
		return isset($this->order) ? ' ORDER BY ' . implode(', ', $this->order) : '';
	}

	public function getLimit(): string
	{
		return $this->limit ?? '';
	}

	/**
	 * Execute a query.
	 * Returns boolean on writes and a Result on reads.
	 */
	public function exec(): bool|Result
	{
		try
		{
			$db = Database::instance();

			$query = $this->buildQuery();

			#PP#begin#
			if ($this->debug)
			{
				printf("<code class=\"gdo-query-debug\">%s</code>\n", html($query));
				Logger::rawLog('query', $query);
			}
			#PP#end#

			if ($this->write)
			{
				return $db->queryWrite($query);
			}
			else
			{
				return new Result($this->fetchTable,
					$db->queryRead($query, $this->buffered), $this->cached);
			}
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

}
