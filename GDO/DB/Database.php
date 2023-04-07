<?php
declare(strict_types=1);
namespace GDO\DB;

use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\DBMS\Module_DBMS;
use Throwable;

/**
 * DB abstraction layer.
 *
 * @version 7.0.3
 * @since 3.0.0
 *
 * @author gizmore
 * @see GDO
 * @see Query
 * @see Result
 * @see GDT_Table
 */
class Database
{

	# Const
	final public const PRIMARY_USING = 'USING HASH'; # Default index algorithm for primary keys.

	# Instance
	public static int $LOCKS = 0;
	public static int $READS = 0;
	public static int $WRITES = 0;

	# DBMS
	public static int $COMMITS = 0;

	# Connection
	public static int $QUERIES = 0; # any dbms provider link. remove?
	public static float $QUERY_TIME = 0.0; # config
	private static Database $INSTANCE; # config
	private static ?Module_DBMS $DBMS = null; # configured db.

	/**
	 * Available GDO classes.
	 *
	 * @var GDO[]
	 */
	private static array $TABLES = []; # current db in use.

	# Debug
	/**
	 * gdoColumns for all GDO.
	 *
	 * @var GDT[]
	 */
	private static array $COLUMNS = []; # Set to 0/off, 1/on, 2/backtraces

	# Performance single db
	public \mysqli|\SQLite3 $link;
	public ?string $usedb;
	public int $locks = 0;
	public int $reads = 0;
	public int $writes = 0;
	public int $commits = 0;

	# Performance summed for all connections
	public int $queries = 0;
	public float $queryTime = 0.0;
	private int $port = 3306;
	private string $host, $user, $pass;
	private ?string $db;
	private int $debug;

	public function __construct(string $host, string $user, string $pass, string $db = null, int $debug = 0)
	{
		self::$INSTANCE = $this;
		$this->debug = $debug;
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}

	public static function init(?string $databaseName = GDO_DB_NAME): self
	{
		Cache::init();
		return new self(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, $databaseName, GDO_DB_DEBUG);
	}

	/**
	 * Clear cache for all GDO.
	 */
	public static function clearCache(): void
	{
		foreach (self::$TABLES as $gdo)
		{
			$gdo->clearCache();
		}
	}

	/**
	 * @return GDT[]
	 */
	public static function &columnsS(string $classname): array
	{
		if (!isset(self::$COLUMNS[$classname]))
		{
			self::$COLUMNS[$classname] = self::hashedColumns(self::tableS($classname));
		}
		return self::$COLUMNS[$classname];
	}

	public static function tableS(string $classname): ?GDO
	{
		if (!isset(self::$TABLES[$classname]))
		{
			/** @var GDO $gdo * */
			$gdo = call_user_func([$classname, 'tableGDO']);

			if ($gdo->gdoAbstract())
			{
				return null;
			}

			self::$TABLES[$classname] = $gdo;

			# Always init a cache item.
			$gdo->initCache();

			# Store hashed columns.
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);
		}
		return self::$TABLES[$classname];
	}

	/**
	 * Extract name from gdo columns for hashmap.
	 *
	 * @return GDT[]
	 */
	private static function hashedColumns(GDO $gdo): array
	{
		$columns = [];
		foreach ($gdo->gdoColumns() as $gdt)
		{
			$columns[$gdt->getName()] = $gdt;
		}
		return $columns;
	}

	public function db(string $db): self
	{
		$this->db = $db;
		return $this;
	}

	public function __destruct()
	{
		$this->closeLink();
	}

	public function closeLink(): void
	{
		if (isset($this->link))
		{
			unset($this->link);
			try
			{
				self::DBMS(false)->dbmsClose();
			}
			catch (GDO_DBException $ex)
			{
				Debug::debugException($ex);
			}
		}
	}

	/**
	 * @throws GDO_DBException
	 */
	public static function DBMS(bool $withLink = true): Module_DBMS
	{
		if (!self::$DBMS)
		{
			self::$DBMS = Module_DBMS::instance();
		}
		if ($withLink)
		{
			self::$INSTANCE->getLink();
		}
		return self::$DBMS;
	}

	#############
	### Query ###
	#############

	public static function instance(): self
	{
		return self::$INSTANCE;
	}

	/**
	 * @throws GDO_DBException
	 */
	public function getLink(): \mysqli|\SQLite3
	{
		if (!isset($this->link))
		{
			$this->link = $this->openLink();
		}
		return $this->link;
	}

	/**
	 * @throws GDO_DBException
	 */
	private function openLink(): \SQLite3|\mysqli
	{
		try
		{
			$t1 = microtime(true); #PP#delete#
			$this->link = $this->connect();
			return $this->link;
		}
		catch (Throwable $e)
		{
			throw new GDO_DBException('err_db_connect', [$e->getMessage()]);
		}
		#PP#start#
		finally
		{
			$timeTaken = microtime(true) - $t1;
			$this->queryTime += $timeTaken;
			self::$QUERY_TIME += $timeTaken;
		}
		#PP#end#
	}

	/**
	 * @throws GDO_DBException
	 */
	public function connect(): \mysqli|\SQLite3
	{
		return self::DBMS(false)->dbmsOpen($this->host, $this->user, $this->pass, $this->db, $this->port);
	}

	public function isConnected(): bool
	{
		return isset($this->link);
	}

	/**
	 * @throws GDO_DBException
	 */
	public function queryRead(string $query, bool $buffered = true): \mysqli_result|\SQLite3Result
	{
		self::$READS++; #PP#delete#
		$this->reads++; #PP#delete#
		return $this->query($query, $buffered);
	}

	###################
	### Table cache ###
	###################

	/**
	 * @throws GDO_DBException
	 */
	private function query(string $query, bool $buffered = true): \mysqli_result|\SQLite3Result|bool
	{
		$dbms = self::DBMS();
		$t1 = microtime(true); #PP#delete#

		$result = $dbms->dbmsQuery($query, $buffered);

		if (!$result)
		{
			$error = $dbms->dbmsError();
			$errno = $dbms->dbmsErrno();
			throw new GDO_DBException('err_db', [$errno, html($error), html($query)]);
		}

		#PP#start#
		$t2 = microtime(true);
		$timeTaken = $t2 - $t1;
		$this->queries++;
		self::$QUERIES++;
		$this->queryTime += $timeTaken;
		self::$QUERY_TIME += $timeTaken;
		if ($this->debug)
		{
			$timeTaken = sprintf('%.04f', $timeTaken);
			Logger::log('queries', '#' . self::$QUERIES .
				": ({$timeTaken}s) " . $query);
			if ($this->debug > 1)
			{
				$trace = Debug::backtrace('#' . self::$QUERIES . ' Backtrace', false);
				$sep = str_repeat('--', 120);
				Logger::log('queries', "{$trace}\n\n{$sep}\n\n");
			}
		}
		#PP#end#
		return $result;
	}

	/**
	 * @throws GDO_DBException
	 */
	public function queryWrite(string $query): bool
	{
		if (GDO_DB_READONLY)
		{
			throw new GDO_DBException('err_db_ro');
		}
		self::$WRITES++; #PP#delete#
		$this->writes++; #PP#delete#
		return $this->query($query, false);
	}

	public function insertId(): int
	{
		try
		{
			return self::DBMS()->dbmsInsertId();
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return -1;
		}
	}

	public function affectedRows(): int
	{
		try
		{
			return self::DBMS()->dbmsAffected();
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return -1;
		}
	}

	####################
	### Table create ###
	####################

	/**
	 * @throws GDO_DBException
	 */
	public function tableExists(string $tableName): bool
	{
		return Database::DBMS()->dbmsTableExists($tableName);
	}

	/**
	 * Create a database table from a GDO.
	 *
	 * @throws GDO_DBException
	 */
	public function createTable(GDO $gdo): void
	{
		try
		{
			$this->disableForeignKeyCheck();
			self::DBMS()->dbmsCreateTable($gdo);
		}
		finally
		{
			$this->enableForeignKeyCheck();
		}
	}

	/**
	 * @throws GDO_DBException
	 */
	public function disableForeignKeyCheck(): void
	{
		$this->enableForeignKeyCheck(false);
	}

	/**
	 * @throws GDO_DBException
	 */
	public function enableForeignKeyCheck(bool $bool = true): void
	{
		Database::DBMS()->dbmsForeignKeys($bool);
	}

	/**
	 * @throws GDO_DBException
	 */
	public function dropTable(GDO $gdo): void
	{
		$this->dropTableName($gdo->gdoTableIdentifier());
	}

	###################
	### DB Creation ###
	###################

	/**
	 * @throws GDO_DBException
	 */
	public function dropTableName(string $tableName): void
	{
		self::DBMS()->dbmsDropTable($tableName);
	}

	public function truncateTable(GDO $gdo): void
	{
		$tableName = $gdo->gdoTableIdentifier();
		self::DBMS()->dbmsTruncateTable($tableName);
	}

	public function createDatabase(string $databaseName): bool
	{
		try
		{
			self::DBMS()->dbmsCreateDB($databaseName);
			return true;
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	###################
	### Transaction ###
	###################

	public function dropDatabase(string $databaseName): bool
	{
		try
		{
			self::DBMS()->dbmsDropDB($databaseName);
			return true;
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	public function useDatabase(string $databaseName): bool
	{
		try
		{
			$this->db = $databaseName;
			if ($this->isConnected())
			{
				self::DBMS()->dbmsUseDB($databaseName);
			}
			return true;
		}
		catch (GDO_DBException $ex)
		{
			$this->db = null;
			Debug::debugException($ex);
			return false;
		}
	}

	public function transactionBegin(): bool
	{
		try
		{
			self::DBMS()->dbmsBegin();
			return true;
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	############
	### Lock ###
	############

	public function transactionEnd(): void
	{
		# Perf
		$this->commits++; #PP#delete#
		self::$COMMITS++; #PP#delete#

		# Exec and perf
		$t1 = microtime(true); #PP#delete#
		self::DBMS()->dbmsCommit();
		$t2 = microtime(true); #PP#delete#
		$tt = $t2 - $t1; #PP#delete#

		# Perf
		$this->queryTime += $tt; #PP#delete#
		self::$QUERY_TIME += $tt; #PP#delete#
	}

	public function transactionRollback(): bool
	{
		try
		{
			self::DBMS()->dbmsRollback();
			return true;
		}
		catch (Throwable $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	###############
	### FKCheck ###
	###############

	public function lock(string $lock, int $timeout = 30): bool
	{
		try
		{
			$this->locks++; #PP#delete#
			self::$LOCKS++; #PP#delete#
			return self::DBMS()->dbmsLock($lock, $timeout);
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	public function unlock(string $lock): bool
	{
		try
		{
			return self::DBMS()->dbmsUnlock($lock);
		}
		catch (GDO_DBException $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	##############
	### Import ###
	##############

	/**
	 * Import a large SQL file.
	 *
	 * @throws GDO_DBException
	 */
	public function parseSQLFile(string $path): void
	{
		Database::DBMS()->dbmsExecFile($path);
	}

}
