<?php
declare(strict_types=1);
namespace GDO\DB;

use GDO\Core\GDO;

/**
 * A Database query result.
 * Use fetchTable() to control the object type for fetching objects.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see ArrayResult
 */
class Result
{

	public GDO $table;

	private bool $useCache;

	private \mysqli_result|\SQLite3Result $result;

	###################
	### Instanciate ###
	###################
	public function __construct(GDO $table, $result, bool $useCache)
	{
		$this->table = $table;
		$this->result = $result;
		$this->useCache = $useCache;
	}

	/**
	 * Shouldn't it be as safe and as fast to just rely on their destructors?
	 */
	public function __destruct()
	{
		if (isset($this->result))
		{
			Database::DBMS()->dbmsFree($this->result);
			unset($this->result);
		}
	}

	################
	### Num rows ###
	################
	public function numRows(): int
	{
		return Database::DBMS()->dbmsNumRows($this->result);
	}

	public function affectedRows(): int
	{
		return Database::DBMS()->dbmsAffected();
	}

	#############
	### Fetch ###
	#############

	public function fetchAllRows(): array
	{
		return Database::DBMS()->dbmsFetchAllRows($this->result);
	}

	public function fetchAllAssoc(): ?array
	{
		return Database::DBMS()->dbmsFetchAllAssoc($this->result);
	}

	public function fetchObject(): ?GDO
	{
		return $this->fetchAs($this->table);
	}

	public function fetchAs(GDO $table): ?GDO
	{
		if ($gdoData = $this->fetchAssoc())
		{
			if ($this->useCache && $table->cached())
			{
				return $table->initCached($gdoData);
			}
			elseif ($table->cached())
			{
				return $table->initCached($gdoData, false);
			}
			else
			{
				$class = $table->gdoClassName();
				/** @var GDO $object * */
				$object = call_user_func([$class, 'entity'], $gdoData);
				return $object->setPersisted();
			}
		}
		return null;
	}

	public function fetchAssoc(): ?array
	{
		return Database::DBMS()->dbmsFetchAssoc($this->result);
	}

	public function fetchInto(GDO $gdo): ?GDO
	{
		if ($gdoVars = $this->fetchAssoc())
		{
			return $gdo->tempReset()->setGDOVars($gdoVars)->setPersisted();
		}
		return null;
	}

	public function fetchAllObjects(bool $json = false): array
	{
		return $this->fetchAllObjectsAs($this->table, $json);
	}

	public function fetchAllObjectsAs(GDO $table, bool $json = false): array
	{
		$objects = [];
		while ($object = $this->fetchAs($table))
		{
			$objects[] = $json ? $object->toJSON() : $object;
		}
		return $objects;
	}

	/**
	 * For a 2 column select.
	 * Fetch all 2 column rows as a 0 => 1 assoc array.
	 */
	public function fetchAllArray2dPair(): array
	{
		$array2d = [];
		while ($row = $this->fetchRow())
		{
			$array2d[$row[0]] = $row[1];
		}
		return $array2d;
	}

	public function fetchRow(): ?array
	{
		return Database::DBMS()->dbmsFetchRow($this->result);
	}

	/**
	 * Fetch all objects and have the ID as array key.
	 *
	 * @return GDO[]
	 */
	public function &fetchAllArray2dObject(GDO $table = null, $json = false): array
	{
		$table = $table ? $table : $this->table;
		$array2d = [];
		while ($object = $this->fetchAs($table))
		{
			$array2d[$object->getID()] = $json ? $object->toJSON() : $object;
		}
		return $array2d;
	}

	/**
	 * @return GDO[]
	 */
	public function fetchAllArrayAssoc2dObject(GDO $table = null): array
	{
		$table = $table ? $table : $this->table;
		$array2d = [];
		$firstKey = '';
		while ($object = $this->fetchAs($table))
		{
			$firstKey = $firstKey ? $firstKey : array_keys($object->getGDOVars())[0];
			$array2d[$object->gdoVar($firstKey)] = $object;
		}
		return $array2d;
	}

	/**
	 * Alias for fetchAllValues().
	 */
	public function fetchColumn(): array
	{
		return $this->fetchAllVars();
	}

	/**
	 * Fetch the first var of the next row.
	 */
	public function fetchVar(): ?string
	{
		$row = $this->fetchRow();
		return $row ? $row[0] : null;
	}

	/**
	 * Fetch all, but only a single column as simple array.
	 */
	public function fetchAllVars(): array
	{
		$values = [];
		while ($value = $this->fetchVar())
		{
			$values[] = $value;
		}
		return $values;
	}

	public function getDummy(): GDO
	{
		return $this->table->cache->getDummy();
	}

}
