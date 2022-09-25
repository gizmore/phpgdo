<?php
namespace GDO\Core;

use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\DB\Query;
use GDO\DB\Result;
use GDO\Date\Time;
use GDO\User\GDO_User;
use GDO\UI\TextStyle;

/**
 * A data exchange object, and...
 * 
 * A GDO is both, a table and an entity.
 * The table GDO just holds the caches and GDT instances.
 * The other entitites have $gdoVars. A DB row.
 * 
 * An array of db vars, which values are backed by a database and caches.
 * DB vars are stored in the $gdoVars array.
 * When a GDT column is used, the $gdoVars are reference-copied into the GDT,
 * which make this framework tick fast with a low memory footprint.
 * It safes memory to only keep the GDTs once per Table.
 * Please note that all vars are considered string in GDOv7, the db representation.
 * The values, like a datetime, are generated by GDT->toValue()
 * 
 * - Offers bulk operations
 * 
 * @author gizmore@wechall.net
 * @version 7.0.1
 * @since 3.0.0
 * @see GDT
 * @see Cache
 * @see Database
 * @see Query
 * @see Result
 * @see WithTemp
 * @see WithModule
 * @license GDOv7-LICENSE
 */
abstract class GDO extends GDT
{
	use WithTemp;
	
	#################
	### Constants ###
	#################
	const TOKEN_LENGTH = 16; # length of gdoHashcode and GDT_Token
	
	const MYISAM = 'MyIsam'; # Faster writes
	const INNODB = 'InnoDB'; # Foreign keys
	const MEMORY = 'Memory'; # Temp tables @TODO Temp memory tables not working? => remove
	
	##############
	### Static ###
	##############
	/**
	 * Get the table GDO for this class.
	 */
	public static function table() : self
	{
		return Database::tableS(static::class);
	}

	#################
	### Construct ###
	#################
	public static int $GDO_COUNT = 0; # total allocs
	public static int $GDO_KILLS = 0; # total deallocs
	public static int $GDO_PEAKS = 0; # max sim. alive
	
	public function __construct()
	{
// 		parent::__construct(); # DO *NOT* call the GDT perf counter!
		$this->afterLoaded();
	}
	
	public function __wakeup()
	{
		$this->recache = false;
		$this->afterLoaded();
	}
	
	public function __destruct()
	{
		self::$GDO_KILLS++;
	}
	
	private function afterLoaded() : void
	{
		self::$GDO_COUNT++;
		$alive = self::$GDO_COUNT - self::$GDO_KILLS;
		if ($alive > self::$GDO_PEAKS)
		{
			self::$GDO_PEAKS = $alive;
		}
		if (self::$GDT_DEBUG)
		{
			$this->logDebug();
		}
	}
	
	private function logDebug() : void
	{
		Logger::log('gdo', sprintf('%d: %s', self::$GDO_COUNT, self::gdoClassNameS()));
		if (self::$GDT_DEBUG >= 2)
		{
			Logger::log('gdo', Debug::backtrace('Backtrace', false));
		}
	}
	
	################
	### Abstract ###
	################
	/**
	 * @return GDT[]
	 */
	public abstract function gdoColumns() : array;
	
	/**
	 * Is this GDO backed by the GDO process cache?
	 */
	public function gdoCached() : bool { return true; }

	/**
	 * Is this GDO backed by the Memcached cache?
	 */
	public function memCached() : bool { return $this->gdoCached() && GDO_MEMCACHE; }

	/**
	 * Is this GDO backed by any cache means?
	 */
	public function cached() : bool { return $this->gdoCached() || $this->memCached(); }
	
	/**
	 * Return the mysql storage engine for this gdo.
	 */
	public function gdoEngine() : string { return GDO_DB_ENGINE; } # @see self::MYISAM
	
	/**
	 * Is this GDO abstract? Required for inheritance hacks.
	 */
	public function gdoAbstract() : bool { return false; }
	
	/**
	 * Indicate that this GDO is only to transfer structured data.
	 * It is not abstract, yet still not persisted.
	 */
	public function gdoDTO() : bool { return false; }
	
	################
	### Escaping ###
	################
	public static function escapeIdentifierS(string $identifier) : string { return str_replace("`", "\\`", $identifier); }
	public static function quoteIdentifierS(?string $identifier) : string { return $identifier; } # performance for features
	public static function escapeSearchS(?string $var) : string { return str_replace(['%', "'", '"'], ['\\%', "\\'", '\\"'], $var); }
	public static function escapeS(?string $var) : string { return str_replace(['\\', "'", '"'], ['\\\\', '\\\'', '\\"'], $var); }
	public static function quoteS(?string $var) : string
	{
		if (is_string($var))
		{
			return '"' . self::escapeS($var) . '"';
		}
		elseif ($var === null)
		{
			return "NULL";
		}
		elseif (is_numeric($var))
		{
			return "$var";
		}
		elseif (is_bool($var))
		{
			return $var ? '1' : '0';
		}
	}
	
	#################
	### Persisted ###
	#################
	private bool $persisted = false;
	public function isPersisted() : bool { return $this->persisted; }
	public function setPersisted(bool $persisted=true) : self
	{
// 		unset($this->id);
		$this->persisted = $persisted;
		return $this;
	}
	
	/**
	 * @TODO: Reset this GDO like it came from the cache (initial/var/dirty)
	 */
// 	public function reset(bool $removeInput=false) : self
// 	{
// 		$this->dirty = false;
// 		foreach ($this->gdoColumnsCache() as $gdt)
// 		{
			
// 		}
// 		parent::reset($removeInput);
// 	}
	
	##############
	### Render ###
	##############
	public function gdoDisplay(string $key) : string
	{
		$var = $this->gdoVar($key);
		return html($var);
	}
	
	public function getName() : ?string
	{
		return $this->renderName();
	}
	
	public function renderName() : string
	{
		return $this->gdoHumanName() . "#" . $this->getID();
	}
	
	public function renderOption() : string
	{
		return $this->renderName();
	}
	
	public function renderJSON()
	{
		return $this->toJSON();
	}
	
	public function renderCLI() : string
	{
		return $this->renderName();
	}
	
	/**
	 * @return GDT[]
	 */
	public function toJSON() : array
	{
		$values = [];
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt->isSerializable())
			{
				$data = $gdt->gdo($this)->getGDOData();
				foreach ($data as $k => $v)
				{
					if ($v !== null)
					{
						$values[$k] = $v;
					}
				}
			}
		}
		return $values;
	}
	
	public function renderVar() : string
	{
		return $this->renderName();
	}
	
	############
	### Vars ###
	############
	/**
	 * Mark vars as dirty.
	 * Either true for all,
	 * false for none,
	 * or an assoc array with field mappings.
	 * @var mixed $dirty
	 */
	private $dirty = false;
	
	/**
	 * DB entity vars.
	 * @var string[string]
	 */
	private array $gdoVars;
	
	public function &getGDOVars() : array { return $this->gdoVars; }
	
	public function hasVar(string $key) : bool
	{
		return array_key_exists($key, $this->gdoVars);
	}
	
	public function hasColumn(string $key) : bool
	{
		return array_key_exists($key, $this->gdoColumnsCache());
	}
	
	public function gdoVar(string $key) : ?string
	{
		return isset($this->gdoVars[$key]) ? $this->gdoVars[$key] : null;
	}
	
	public function gdoVars(array $keys) : array
	{
		return array_combine($keys, array_map(function($key) {
			return $this->gdoVar($key);
		}, $keys));
	}
	
	/**
	 * Break these GDT functions as they confuse you now.
	 */
	public function getVar()
	{
		throw new GDO_Error('err_gdo_no_gdt', ['getVar', $this->gdoHumanName()]);
	}
	
	/**
	 * Break these GDT functions as they confuse you now.
	 */
	public function getValue(string $var = null)
	{
		throw new GDO_Error('err_gdo_no_gdt', ['getValue', $this->gdoHumanName()]);
	}
	
	/**
	 * @param string $key
	 * @param string $var
	 * @param boolean $markDirty
	 * @return self
	 */
	public function setVar(string $key, string $var=null, bool $markDirty=true) : self
	{
		# @TODO: Better use temp? @see Vote/Up
		if (!$this->hasColumn($key))
		{
			$this->gdoVars[$key] = $var;
			return $this;
		}
		
		$gdt = $this->gdoColumn($key)->var($var);
		$d = false;
		$data = $gdt->getGDOData();
// 		{
			foreach ($data as $k => $v)
			{
				if ($this->gdoVars[$k] !== $v)
				{
					$this->gdoVars[$k] = $v === null ? null : (string)$v;
					$d = true;
				}
			}
// 		}
		return ($markDirty && $d) ? $this->markDirty($key) : $this;
	}
	
	public function setVars(array $vars=null, $markDirty=true) : self
	{
		foreach ($vars as $key => $value)
		{
			$this->setVar($key, $value, $markDirty);
		}
		return $this;
	}
	
	public function setValue(string $key, $value, bool $markDirty=true) : self
	{
		$vars = $this->gdoColumn($key)->value($value)->getGDOData();
		$this->setVars($vars, $markDirty);
		return $this;
	}
	
	public function setGDOVars(array $vars, bool $dirty=false) : self
	{
// 		unset($this->id);
		$this->gdoVars = $vars;
		return $this->dirty($dirty);
	}
	
	/**
	 * Get the gdo value of a column.
	 * @param string $key
	 * @return mixed
	 */
	public function gdoValue(string $key)
	{
		return $this->gdoColumn($key)->getValue();
	}
	
	public function inputs(array $inputs=null) : self
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			$gdt->inputs($inputs);
		}
		return $this;
	}
	
	#############
	### Dirty ###
	#############
	public function markClean(string $key) : self
	{
		if ($this->dirty === false)
		{
			$this->dirty = array_keys($this->gdoVars);
			unset($this->dirty[$key]);
		}
		elseif (is_array($this->dirty))
		{
			unset($this->dirty[$key]);
		}
		return $this;
	}
	
	public function markDirty(string $key) : self
	{
		if ($this->dirty === false)
		{
			$this->dirty = [];
		}
		if ($this->dirty !== true)
		{
			$this->dirty[$key] = true;
		}
		return $this;
	}
	
	public function isDirty() : bool
	{
		return is_bool($this->dirty) ? $this->dirty : (count($this->dirty) > 0);
	}
	
	/**
	 * Get gdoVars that have been changed.
	 * @return string[]
	 */
	public function getDirtyVars() : array
	{
		if ($this->dirty === true)
		{
			$vars = [];
			foreach ($this->gdoColumnsCache() as $gdt)
			{
				$data = $gdt->gdo($this)->getGDOData();
				foreach ($data as $k => $v)
				{
					$vars[$k] = $v;
				}
			}
			return $vars;
		}
		elseif ($this->dirty === false)
		{
			return [];
		}
		else
		{
			$vars = [];
			foreach (array_keys($this->dirty) as $name)
			{
				if ($data = $this->gdoColumn($name)->getGDOData())
				{
					foreach ($data as $k => $v)
					{
						$vars[$k] = $v;
					}
				}
			}
			return $vars;
		}
	}
	
	###############
	### Columns ###
	###############
	/**
	 * Get the first primary key column
	 * @return GDT
	 */
	public function gdoPrimaryKeyColumn() : ?GDT
	{
		foreach ($this->gdoColumnsCache() as $column)
		{
			if ($column->isPrimary())
			{
				return $column;
			}
		}
		return null;
	}
	
	/**
	 * Get the primary key columns for a table.
	 * @return GDT[]
	 */
	public function gdoPrimaryKeyColumns() : array
	{
		$cache = self::table()->cache;
		
		if (isset($cache->pkColumns))
		{
			return $cache->pkColumns;
		}
		
		$columns = [];
		foreach ($this->gdoColumnsCache() as $column)
		{
			if ($column->isPrimary())
			{
				$columns[$column->name] = $column;
			}
			else
			{
				break; # early break is possible because we start all tables with their PKs.
			}
		}
		
		if (empty($columns))
		{
			$columns = $this->gdoColumnsCache();
		}
		
		$cache->pkColumns = $columns;
		
		return $columns;
	}
	
	public function gdoPrimaryKeyValues() : array
	{
		$values = [];
		foreach ($this->gdoPrimaryKeyColumns() as $gdt)
		{
			$values[$gdt->name] = $this->gdoVar($gdt->name);
		}
		return $values;
	}
	
	/**
	 * Get primary key column names.
	 * @return string[]
	 */
	public function gdoPrimaryKeyColumnNames() : array
	{
		$table = self::table();
		$cache = isset($table->cache) ? $table->cache : null;
		
		if ($cache && isset($cache->pkNames))
		{
			return $cache->pkNames;
		}
		
		$names = [];
		foreach ($this->gdoColumnsCache() as $column)
		{
			if ($column->isPrimary())
			{
				$names[] = $column->name;
			}
			else
			{
				break; # Assume PKs are first until no more PKs
			}
		}
		
		if (empty($names))
		{
			$names = array_map(function(GDT $gdt){
				return $gdt->getName();
			}, $this->gdoColumnsCache());
		}
		
		if ($cache)
		{
			$cache->pkNames = $names;
		}
		
		return $names;
	}
	
	#################
	### Column Of ###
	#################
	/**
	 * Get the first column of a specified GDT.
	 * Useful to make GDTs more automated. E.g. The auto inc column syncs itself on gdoAfterCreate.
	 */
	public function gdoColumnOf(string $className) : ?GDT
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if (is_a($gdt, $className, true))
			{
				return $gdt->gdo($this);
			}
		}
		return null;
	}
	
	/**
	 * Get all columns of a type. Used to load users via two different GDT_Name fields.
	 * @return GDT[]
	 */
	public function gdoColumnsOf(string $className) : array
	{
		$back = [];
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if (is_a($gdt, $className, true))
			{
				$back[$gdt->getName()] = $gdt->gdo($this);
			}
		}
		return $back;
	}
	
	public function gdoVarOf(string $className) : ?string
	{
		return $this->gdoVar($this->gdoColumnOf($className)->name);
	}
	
	public function gdoValueOf(string $className)
	{
		return $this->gdoColumnOf($className)->getValue();
	}
	
	/**
	 * Get the GDOs AutoIncrement column, if any.
	 * @return GDT_AutoInc
	 */
	public function gdoAutoIncColumn() : GDT { return $this->gdoColumnOf(GDT_AutoInc::class); }
	
	/**
	 * Get the GDOs name identifier column, if any.
	 * @return GDT_Name
	 */
	public function gdoNameColumn() : ?GDT_Name
	{
		return $this->gdoColumnOf(GDT_Name::class);
	}
	
	/**
	 * Get a GDT column by name.
	 */
	public function getColumn(string $key) : GDT
	{
		return $this->gdoColumnsCache()[$key];
	}
	
	/**
	 * Get the GDT column for a key.
	 * Assign my GDO values to the GDT.
	 */
	public function gdoColumn(string $key, bool $throw=true) : GDT
	{
		if ($gdt = @$this->gdoColumnsCache()[$key])
		{
			return $gdt->gdo($this); # assign values
		}
		elseif ($throw)
		{
			throw new GDO_Error('err_unknown_gdo_column', [$this->gdoClassName(), html($key)]);
		}
		return null;
	}

	/**
	 * Copy a GDT column and assign my values.
	 */
	public function gdoColumnCopy(string $key, bool $throw=true) : GDT
	{
		if ($gdt = $this->gdoColumnsCache()[$key])
		{
			$column = clone $gdt;
			return $column->gdo($this);
		}
		elseif ($throw)
		{
			throw new GDO_Error('err_unknown_gdo_column', [$this->renderName(), html($key)]);
		}
		return null;
	}
	
	/**
	 * Get only wanted GDT columns.
	 * @return GDT[]
	 */
	public function gdoColumnsOnly(string...$keys) : array
	{
		return $this->gdoColumnsOnlyExcept($keys, true);
	}
	
	/**
	 * Get all GDT columns except those listed.
	 * @return GDT[]
	 */
	public function gdoColumnsExcept(string...$keys) : array
	{
		return $this->gdoColumnsOnlyExcept($keys, false);
	}
	
	private function gdoColumnsOnlyExcept(array $keys, bool $negate) : array
	{
		$columns = [];
		foreach (array_keys($this->gdoColumnsCache()) as $key)
		{
			if (in_array($key, $keys, true) === $negate)
			{
				$columns[$key] = $this->gdoColumn($key);
			}
		}
		return $columns;
	}
	
	##########
	### DB ###
	##########
	/**
	 * Create a new query for this GDO table.
	 * @return Query
	 */
	public function query() : Query
	{
		return new Query(self::table());
	}
	
	/**
	 * Find a row by AutoInc Id.
	 * @param string $id
	 * @return static
	 */
	public function find(string $id=null, bool $throw=true) : self
	{
		if ($id && ($gdo = $this->getById($id)))
		{
			return $gdo;
		}
		if ($throw)
		{
			self::notFoundException(html($id));
		}
	}
	
	public function findCached(string...$ids) : ?self
	{
		if (!($gdo = $this->table()->cache->findCached(...$ids)))
		{
			$gdo = self::getById(...$ids);
		}
		return $gdo;
	}
	
	public function countWhere(string $condition='true') : int
	{
		return $this->select('COUNT(*)', false)->where($condition)->
		noOrder()->exec()->fetchValue();
	}
	
	/**
	 * Find a row by condition. Throws GDO::notFoundException.
	 */
	public function findWhere(string $condition) : ?self
	{
		if (!($gdo = $this->getWhere($condition)))
		{
			self::notFoundException(html($condition));
		}
		return $gdo;
	}
	
	/**
	 * Get a row by condition.
	 */
	public function getWhere(string $condition) : ?self
	{
		return $this->select()->where($condition)->
			first()->exec()->fetchObject();
	}
	
	public function select(string $columns='*', bool $withHooks=true) : Query
	{
		$query = $this->query()->select($columns)->from($this->gdoTableIdentifier());
		if ($withHooks)
		{
			$this->beforeRead($query);
		}
		return $query;
	}
	
	################
	### Validate ###
	################
	public function isValid() : bool
	{
		$invalid = 0;
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			$invalid += $gdt->noError()->gdo($this)->validated() ? 0 : 1;
		}
		return $invalid === 0;
	}
	
	##############
	### Delete ###
	##############
	/**
	 * Delete this entity.
	 */
	public function delete(bool $withHooks=true) : self
	{
		return $this->deleteB($withHooks);
	}
	
	/**
	 * Check if we are deleted.
	 */
	public function isDeleted() : bool
	{
		if ($gdt = $this->gdoColumnOf(GDT_DeletedAt::class))
		{
			return $gdt->getVar() !== null;
		}
		if ($gdt = $this->gdoColumnOf(GDT_DeletedBy::class))
		{
			return $gdt->getVar() !== null;
		}
		return !$this->isPersisted();
	}
	
	/**
	 * Mark this GDO as deleted, or delete physically.
	 */
	public function markDeleted(bool $withHooks=true) : self
	{
		if ($gdt = $this->gdoColumnOf(GDT_DeletedAt::class))
		{
			$this->setVar($gdt->name, Time::getDate());
			$change = true;
		}
		if ($gdt = $this->gdoColumnOf(GDT_DeletedBy::class))
		{
			$this->setVar($gdt->name, GDO_User::current()->getID());
			$change = true;
		}
		if ($change)
		{
			$this->save($withHooks);
			if ($withHooks)
			{
				$this->afterDelete(true);
			}
			return $this;
		}
		else
		{
			return $this->deleteB($withHooks);
		}
	}
	
	/**
	 * Delete multiple rows, but still one by one to trigger all events correctly.
	 * 
	 * @return int number of deleted rows
	 */
	public function deleteWhere(string $condition, bool $withHooks=true) : int
	{
		$deleted = 0;
		if ($withHooks)
		{
			$result = $this->table()->select()->where($condition)->exec();
			while ($gdo = $result->fetchObject())
			{
				$deleted++;
				$gdo->deleteB();
			}
		}
		else
		{
			if ($this->query()->
			delete($this->gdoTableIdentifier())->
			where($condition)->exec())
			{
				$deleted = Database::instance()->affectedRows();
			}
		}
		return $deleted;
	}
	
	private function deleteB(bool $withHooks=true) : self
	{
		if ($this->persisted)
		{
			$query = $this->query()->delete($this->gdoTableIdentifier())->where($this->getPKWhere());
			if ($withHooks)
			{
				$this->beforeDelete($query);
			}
			$query->exec();
			$this->persisted = false;
			if ($withHooks)
			{
				$this->afterDelete();
			}
			$this->uncache();
		}
		return $this;
	}
	
	###############
	### Replace ###
	###############
	public function insert(bool $withHooks=true) : self
	{
		$query = $this->query()->
			insert($this->gdoTableIdentifier())->
			values($this->getDirtyVars());
		return $this->insertOrReplace($query, $withHooks);
	}
	
	public function replace(bool $withHooks=true) : self
	{
		# Check for empty id.
		# Checking for $persisted is wrong, as replace rows can be constructed from scratch.
		$id = $this->getID();
		if ( (!$id) || preg_match('#^[:0]+$#D', $id) )
		{
			return $this->insert($withHooks);
		}
		
		$query = $this->query()->
			replace($this->gdoTableIdentifier())->
			values($this->gdoPrimaryKeyValues())->
			values($this->getDirtyVars());
		
		return $this->insertOrReplace($query, $withHooks);
	}
	
	private function insertOrReplace(Query $query, bool $withHooks) : self
	{
		if ($withHooks)
		{
			$this->beforeCreate($query);
		}
		$query->exec();
		$this->dirty = false;
		$this->persisted = true;
		if ($withHooks)
		{
			$this->afterCreate();
			$this->cache(); # not needed for new rows?
		}
		return $this;
	}
	
	##############
	### Update ###
	##############
	/**
	 * Build a generic update query for the whole table.
	 * @return Query
	 */
	public function update() : Query
	{
		return $this->query()->update($this->gdoTableIdentifier());
	}
	
	public function deleteQuery() : Query
	{
		return $this->query()->delete($this->gdoTableName());
	}
	
	/**
	 * Build an entity update query.
	 */
	public function updateQuery() : Query
	{
		return $this->entityQuery()->update($this->gdoTableIdentifier());
	}
	
	/**
	 * Save this entity.
	 */
	public function save(bool $withHooks=true) : self
	{
		if (!$this->persisted)
		{
			return $this->insert($withHooks);
		}
		if ($setClause = $this->getSetClause())
		{
			$query = $this->updateQuery()->set($setClause);
			
			if ($withHooks)
			{
				$this->beforeUpdate($query);
			}
			
			$query->exec();
			
			$this->dirty = false;
			
			if ($withHooks)
			{
				$this->afterUpdate();
				$this->recache(); # save is the only action where we recache!
			}
		}
		return $this;
	}
	
	########################
	### Var manipulation ###
	########################
	public function increase(string $key, float $by=1) : self
	{
		return $by == 0 ? $this : $this->saveVar($key, $this->gdoVar($key) + $by);
	}
	
	public function saveVar(string $key, ?string $var, bool $withHooks=true, bool &$worthy=false) : self
	{
		return $this->saveVars([$key => $var], $withHooks, $worthy);
	}
	
	/**
	 * @param string[string] $vars
	 */
	public function saveVars(array $vars, bool $withHooks=true, bool &$worthy=false) : self
	{
		$worthy = false; # Anything changed?
		$query = $this->updateQuery();
		foreach ($vars as $key => $var)
		{
			if (array_key_exists($key, $this->gdoVars))
			{
				if ($var !== $this->gdoVars[$key])
				{
					$query->set(sprintf("%s=%s", $key, self::quoteS($var)));
					$this->markClean($key);
					$worthy = true; # We got a change
				}
			}
		}
		
		if (!$worthy)
		{
			return $this;
		}

// 		# Not persisted, insert it.
// 		if (!$this->persisted)
// 		{
// 			return $this->insert($withHooks);
// 		}
		
		# Call hooks even when not needed. Because its needed on GDT_Files
		if ($withHooks)
		{
			$this->beforeUpdate($query); # Can do trickery here... not needed?
		}
		
		$query->exec();
		
		foreach ($vars as $key => $var)
		{
			$this->gdoVars[$key] = $var;
		}
	
		if ($withHooks)
		{
			$this->recache();
		}
		
		# Call hooks even when not needed. Because its needed on GDT_Files
		if ($withHooks)
		{
			$this->afterUpdate();
		}
		
		return $this;
	}
	
	public function saveValue(string $key, $value, bool $withHooks=true) : self
	{
		$var = $this->gdoColumn($key)->toVar($value);
		return $this->saveVar($key, $var, $withHooks);
	}
	
	public function saveValues(array $values, bool $withHooks=true) : self
	{
		$vars = [];
		foreach ($values as $key => $value)
		{
			$this->gdoColumn($key)->setGDOValue($value);
			$vars[$key] = $this->gdoVar($key);
		}
		return $this->saveVars($vars, $withHooks);
	}
	
	public function entityQuery() : Query
	{
		if (!$this->persisted)
		{
			throw new GDO_Error('err_save_unpersisted_entity', [$this->gdoClassName()]);
		}
		return $this->query()->where($this->getPKWhere());
	}
	
	public function getSetClause() : string
	{
		$setClause = '';
		if ($this->dirty !== false)
		{
			foreach ($this->gdoColumnsCache() as $column)
			{
				if (!$column->isVirtual())
				{
					if ( ($this->dirty === true) || (isset($this->dirty[$column->name])) )
					{
						foreach ($column->gdo($this)->getGDOData() as $k => $v)
						{
							if ($setClause !== '')
							{
								$setClause .= ',';
							}
							$setClause .= $k . '=' . self::quoteS($v);
						}
					}
				}
			}
		}
		return $setClause;
	}
	
	####################
	### Primary Keys ###
	####################
	/**
	 * Get the primary key where condition for this row.
	 */
	public function getPKWhere() : string
	{
		$where = '';
		foreach ($this->gdoPrimaryKeyColumns() as $column)
		{
			if ($where)
			{
				$where .= ' AND ';
			}
			$where .= $column->identifier() . ' = ' . $this->quoted($column->name);
		}
		return $where;
	}
	
	public function quoted($key) { return self::quoteS($this->gdoVar($key)); }
	
	################
	### Instance ###
	################
	public static function make(string $name=null) : GDT
	{
		return new static();
	}
	
	/**
	 * @param string[string] $gdoVars
	 */
	public static function entity(array $gdoVars) : self
	{
		$instance = new static();
		$instance->gdoVars = $gdoVars;
		return $instance;
	}
	
	/**
	 * Raw initial string data.
	 * @TODO throw error on unknown initial vars.
	 * @param string[string] $initial gdovars data to copy
	 * @return string[string] the new blank data
	 */
	public static function getBlankData(array $initial = null) : array
	{
		$table = self::table();
		
		$gdoVars = [];
		foreach ($table->gdoColumnsCache() as $gdt)
		{
			# Pass 1) Plug initial var
			$name = $gdt->getName();
			if (isset($initial[$name]))
			{
				$gdt->var($initial[$name]);
			}
			else
			{
				$gdt->reset();
			}

			# Pass 2) Loop over vars
			if ($data = $gdt->blankData())
			{
				foreach ($data as $k => $v)
				{
					if (isset($initial[$k]))
					{
						# override with initial
						$gdoVars[$k] = (string) $initial[$k];
					}
					else
					{
						# Use blank data as is
						$gdoVars[$k] = $v;
					}
				}
			}
		}
		return $gdoVars;
	}
	
	/**
	 * Create a new entity instance.
	 */
	public static function blank(array $initial = null) : self
	{
		return self::entity(self::getBlankData($initial))->dirty()->setPersisted(false);
	}
	
	public function dirty($dirty=true) : self
	{
		$this->dirty = $dirty;
		return $this;
	}
	
	##############
	### Get ID ###
	##############
	/**
	 * Id cache
	 */
// 	private string $id;

	/**
	 * Get the ID for this entity.
	 */
	public function getID() : ?string
	{
// 		if (isset($this->id))
// 		{
// 			return $this->id;
// 		}
		$id = '';
		foreach ($this->gdoPrimaryKeyColumnNames() as $name)
		{
// 			if ($name)
			{
				$id2 = $this->gdoVar($name);
				$id = $id ? "{$id}:{$id2}" : $id2;
			}
		}
// 		if ($id)
// 		{
// 			$this->id = $id;
// 		}
		return $id;
	}
	
	##############
	### Get by ###
	##############
	/**
	 * Get a row by a single arbritary column value.
	 * Try caches first.
	 */
	public static function getBy(string $key, string $var) : ?self
	{
		if ($gdo = self::getCachedBy($key, $var))
		{
			return $gdo;
		}
		return self::table()->getWhere($key . '=' . self::quoteS($var));
	}
	
	private static function getCachedBy(string $key, string $var) : ?self
	{
		return self::table()->cache->getCachedBy($key, $var);
	}
	
	/**
	 * Get a row by a single column value.
	 * Throw exception if not found.
	 */
	public static function findBy(string $key, string $var) : self
	{
		if (!($gdo = self::getBy($key, $var)))
		{
			self::notFoundException($var);
		}
		return $gdo;
	}
	
	/**
	 * @param string[string] $vars
	 */
	public static function getByVars(array $vars) : ?self
	{
		$query = self::table()->select();
		foreach ($vars as $key => $value)
		{
			$query->where(self::quoteIdentifierS($key) . '=' . self::quoteS($value));
		}
		return $query->first()->exec()->fetchObject();
	}
	
	/**
	 * Get a row by IDs.
	 * Tries GDO process cache first.
	 */
	public static function getById(string...$id) : ?self
	{
		$table = self::table();
		if ( (!$table->cached()) || (!($object = $table->cache->findCached(...$id))) )
		{
			$i = 0;
			$query = $table->select();
			foreach ($table->gdoPrimaryKeyColumns() as $gdt)
			{
				$condition = $table->gdoTableName() . '.' . $gdt->identifier() .
				'=' . self::quoteS($id[$i++]);
				$query->where($condition);
			}
			$object = $query->first()->exec()->fetchObject();
		}
		return $object;
	}
	
	public static function findById(string...$id) : self
	{
		if ($object = self::getById(...$id))
		{
			return $object;
		}
		self::notFoundException(implode(':', $id));
	}
	
	public static function findByGID(string $id) : self
	{
		return self::findById(...explode(':', $id));
	}
	
	public static function notFoundException(string $id) : void
	{
		throw new GDO_Error('err_gdo_not_found', [
			TextStyle::bold(self::table()->gdoHumanName()),
			TextStyle::boldi(html($id)),
		]);
	}
	
	/**
	 * Fetch from result set as this table.
	 */
	public function fetch(Result $result) : ?self
	{
		return $result->fetchAs($this);
	}
	
	public function fetchAll(Result $result) : array
	{
		$back = [];
		while ($gdo = $this->fetch($result))
		{
			$back[] = $gdo;
		}
		return $back;
	}
	
	#############
	### Cache ###
	#############
	public Cache $cache;
	
	public function initCache() : void
	{
		$this->cache = new Cache($this);
	}
	
	public function initCached(array $row, bool $useCache=true) : self
	{
		return $this->memCached() ?
			$this->cache->initGDOMemcached($row, $useCache) :
			$this->cache->initCached($row, $useCache);
	}
	
	public function gkey() : string
	{
		$gkey = self::table()->cache->tableName . $this->getID();
		return $gkey;
	}
	
	public function reload($id) : self
	{
		$table = self::table();
		if ($table->cached() && $table->cache->hasID($id))
		{
			$i = 0;
			$id = explode(':', $id);
			$query = $this->select();
			foreach ($this->gdoPrimaryKeyColumns() as $gdt)
			{
				$query->where($gdt->identifier() . '=' . self::quoteS($id[$i++]));
			}
			$object = $query->uncached()->first()->exec()->fetchObject();
			return $object ? $table->cache->recache($object) : null;
		}
	}
	
	/**
	 * This function triggers a recache, also over IPC, if IPC is enabled.
	 */
	public function recache() : self
	{
		if ($this->cached())
		{
			self::table()->cache->recache($this);
		}
		return $this;
	}
	
	public function recacheMemcached() : void
	{
		if ($this->memCached())
		{
			$this->table()->cache->recache($this);
		}
	}
	
	public bool $recache; // @TODO move GDO->$recache to the Cache to reduce GDO by one field
	public function recaching() : self
	{
		$this->recache = true;
		return $this;
	}
	
	public function cache() : void
	{
		if ($this->cached())
		{
			self::table()->cache->recache($this);
		}
	}

	public function uncache() : void
	{
		if ($this->table()->cache)
		{
			$this->table()->cache->uncache($this);
		}
	}
	
	public function clearCache() : self
	{
// 		unset($this->id);
		if ($this->cached())
		{
			$cache = self::table()->cache;
			$cache->clearCache();
			Cache::flush(); # @TODO Find a way to only remove memcached entries for this single GDO.
		}
		return $this;
	}
	
	###########
	### All ###
	###########
	/**
	 * @return self[]
	 */
	public function &all(string $order=null, bool $json=false) : array
	{
		$order = $order ? $order : $this->gdoPrimaryKeyColumn()->name;
		return self::allWhere('true', $order, $json);
	}
	
	/**
	 * @return self[]
	 */
	public function &allWhere($condition='true', $order=null, $json=false) : array
	{
		return self::table()->select()->
		where($condition)->order($order)->
		exec()->fetchAllArray2dObject(null, $json);
	}
	
	public function uncacheAll() : self
	{
		unset($this->table()->cache->all);
		Cache::remove($this->cacheAllKey());
		return $this;
	}
	
	public function cacheAllKey() : string
	{
		return 'all_' . $this->gdoTableName();
	}
	
	public function allCachedExpiration(int $expire=GDO_MEMCACHE_TTL) : void
	{
		$this->cache->expiration = $expire;
	}
	
	/**
	 * Get all rows via allcache.
	 * @return self[]
	 */
	public function &allCached(string $order=null, bool $json=false) : array
	{
		if ($this->cached())
		{
			# Already cached
			$cache = self::table()->cache;
			if (isset($cache->all))
			{
				return $cache->all;
			}
		}
		else
		{
			# No caching at all
			return $this->select()->order($order)->exec()->fetchAllArray2dObject(null, $json);
		}
		
		if (!$this->memCached())
		{
			# GDO cached
			$all = $this->select()->order($order)->exec()->fetchAllArray2dObject(null, $json);
			$cache->all = $all;
			return $all;
		}
		else
		{
			# Memcached
			$key = $this->cacheAllKey();
			if (null === ($all = Cache::get($key)))
			{
				$all = $this->select()->order($order)->exec()->fetchAllArray2dObject(null, $json);
				Cache::set($key, $all);
			}
			$cache->all = $all;
			return $all;
		}
	}
	
	public function removeAllCache() : void
	{
		$key = 'all_' . $this->gdoTableName();
		Cache::remove($key);
	}
	
	##############
	###  Table ###
	##############
	/**
	 * Get the table GDO for a classname.
	 */
	public static function tableFor(string $className, bool $throw=true) : ?self
	{
		$gdo = Database::tableS($className);
		if ($throw && (!$gdo))
		{
			throw new GDO_Error('err_table_gdo', [html($className)]);
		}
		return $gdo;
	}
	
	public function gdoTableName() : string { return $this->table()->cache->tableName; }
	public function gdoTableIdentifier() : string { return $this->gdoTableName(); }
	
	/**
	 * Check if this gdo row entity is the table GDO.
	 * This is done via the always generated cache object and should be efficient. The memory cost for the old private $isTable was horrible!
	 */
	public function gdoIsTable() : bool
	{
		return $this->table()->cache->isTable($this);
	}
	
	public function createTable(bool $reinstall=false) : bool
	{
		if (!($db = Database::instance()))
		{
			die('gdo database not configured!');
		}
		return !!$db->createTable($this);
	}
	public function truncate() : bool { return !!Database::instance()->truncateTable($this); }
	public function dropTable() : bool { return !!Database::instance()->dropTable($this); }
	
	/**
	 * Get all GDT for a GDO.
	 * @return GDT[]
	 */
	public function &gdoColumnsCache() : array
	{
		return Database::columnsS(static::class);
	}
	
	/**
	 * @return GDT[]
	 */
	public function getGDOColumns(array $names) : array
	{
		$columns = [];
		foreach ($names as $key)
		{
			$columns[$key] = $this->gdoColumn($key);
		}
		return $columns;
	}
	
	##############
	### Events ###
	##############
	private function beforeCreate(Query $query) : void
	{
		$this->beforeEvent('gdoBeforeCreate', $query);
	}
	
	private function beforeRead(Query $query) : void
	{
		$this->beforeEvent('gdoBeforeRead', $query);
	}
	
	private function beforeUpdate(Query $query) : void
	{
		$this->beforeEvent('gdoBeforeUpdate', $query);
	}
	
	private function beforeDelete(Query $query) : void
	{
		$this->beforeEvent('gdoBeforeDelete', $query);
	}
	
	private function beforeEvent(string $methodName, Query $query) : self
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			call_user_func([$gdt->gdo($this), $methodName], $this, $query);
		}
		call_user_func([$this, $methodName], $this, $query);
		return $this;
	}
	
	private function afterCreate() : void
	{
		# Flags
		$this->dirty = false;
		$this->setPersisted();
		# Trigger event for GDT like AutoInc, EditedAt, CreatedBy, etc.
		$this->afterEvent('gdoAfterCreate');
	}
	
	private function afterRead() : void
	{
		$this->afterEvent('gdoAfterRead');
	}
	
	private function afterUpdate() : void
	{
		# Flags
		$this->dirty = false;
		$this->afterEvent('gdoAfterUpdate');
	}
	
	private function afterDelete(bool $persisted=false) : void
	{
		# Flags
		$this->dirty = false;
		$this->persisted = $persisted;
		$this->afterEvent('gdoAfterDelete');
	}
	
	private function afterEvent(string $methodName) : void
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			call_user_func([$gdt->gdo($this), $methodName], $this);
		}
		call_user_func([$this, $methodName], $this);
	}
	
	################
	### Hashcode ###
	################
	/**
	 * Generate a hashcode from gdo vars.
	 * This is often used in approval tokens or similar.
	 */
	public function gdoHashcode() : string
	{
		return self::gdoHashcodeS($this->gdoVars);
	}
	
	/**
	 * Generate a hashcode from an associative array.
	 */
	public static function gdoHashcodeS(array $gdoVars) : string
	{
		return substr(sha1(GDO_SALT.json_encode($gdoVars)), 0, self::TOKEN_LENGTH);
	}
	
	#############
	### Order ###
	#############
	public function getDefaultOrder() : ?string
	{
		foreach ($this->gdoColumnsCache() as $gdt)
		{
			if ($gdt->orderable)
			{
				return $gdt->name;
			}
		}
		return null;
	}
	
	#######################
	### Bulk Operations ###
	#######################
	/**
	 * Mass insertion.
	 * @param GDT[] $fields
	 */
	public static function bulkReplace(array $fields, array $data, int $chunkSize=100) : void
	{
		self::bulkInsert($fields, $data, $chunkSize, 'REPLACE');
	}
	
	public static function bulkInsert(array $fields, array $data, $chunkSize=100, $insert='INSERT')
	{
		foreach (array_chunk($data, $chunkSize) as $chunk)
		{
			self::_bulkInsert($fields, $chunk, $insert);
		}
	}
	
	private static function _bulkInsert(array $fields, array $data, string $insert='INSERT') : bool
	{
		$names = [];
		$table = self::table();
		foreach ($fields as $field)
		{
			$names[] = $field->name;
		}
		$names = implode('`, `', $names);
		
		$values = [];
		foreach ($data as $row)
		{
			$brow = [];
			foreach ($row as $col)
			{
				$brow[] = self::quoteS($col);
			}
			$values[] = implode(',', $brow);
		}
		$values = implode("),\n(", $values);
		
		$query = "$insert INTO {$table->gdoTableIdentifier()} (`$names`)\n VALUES\n($values)";
		Database::instance()->queryWrite($query);
		return true;
	}
	
}
