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
 * mySQLi abstraction layer.
 *
 * @TODO support postgres? This can be achieved via making module DB a separate module. Just need to move some classes to core and ifelse them in creation code?
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
	public const PRIMARY_USING = 'USING HASH'; # Default index algorithm for primary keys.

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
	public $link;
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
	private int $debug = 0;

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
		return new self(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, $databaseName, intval(GDO_DB_DEBUG));
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
			$gdo = self::tableS($classname);
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);
		}
		return self::$COLUMNS[$classname];
	}

	public static function tableS(string $classname, bool $initCache = true): ?GDO
	{
		if (!isset(self::$TABLES[$classname]))
		{
			/** @var $gdo GDO * */
			self::$TABLES[$classname] = $gdo = new $classname();

			if ($gdo->gdoAbstract())
			{
				return null;
			}

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
			if ($name = $gdt->getName())
			{
				$columns[$name] = $gdt;
			}
			else
			{
				$columns[] = $gdt;
			}
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
			self::DBMS(false)->dbmsClose();
			unset($this->link);
		}
	}

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

	public function getLink()
	{
		if (!isset($this->link))
		{
			$this->link = $this->openLink();
		}
		return $this->link;
	}

	private function openLink()
	{
		try
		{
			$t1 = microtime(true); #PP#delete#
			if ($this->link = $this->connect())
			{
				return $this->link;
			}
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

	public function connect()
	{
		return self::DBMS(false)->dbmsOpen($this->host, $this->user, $this->pass, $this->db, $this->port);
	}

	public function isConnected(): bool
	{
		return isset($this->link);
	}

	public function queryRead(string $query, bool $buffered = true)
	{
		self::$READS++; #PP#delete#
		$this->reads++; #PP#delete#
		return $this->query($query, $buffered);
	}

	###################
	### Table cache ###
	###################

	private function query(string $query, bool $buffered = true)
	{
		$t1 = microtime(true); #PP#delete#

		$result = self::DBMS()->dbmsQuery($query, $buffered);

		if (!$result)
		{
			if ($this->link)
			{
				$error = self::DBMS()->dbmsError($this->link);
				$errno = self::DBMS()->dbmsErrno($this->link);
			}
			else
			{
				$error = t('err_db_no_link');
				$errno = 0;
			}
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
				$sep = str_repeat('- ', 80);
				Logger::log('queries', "{$trace}\n\n{$sep}\n\n");
			}
		}
		#PP#end#
		return $result;
	}

	public function queryWrite($query)
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
		return self::DBMS()->dbmsInsertId();
	}

	public function affectedRows(): int
	{
		return self::DBMS()->dbmsAffected();
	}

	####################
	### Table create ###
	####################

	public function tableExists(string $tableName): bool
	{
		return Database::DBMS()->dbmsTableExists($tableName);
	}

	/**
	 * Create a database table from a GDO.
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

	public function
	disableForeignKeyCheck()
	{
		return $this->enableForeignKeyCheck(false);
	}

	public function enableForeignKeyCheck(bool $bool = true): void
	{
		Database::DBMS()->dbmsForeignKeys($bool);
	}

	public function dropTable(GDO $gdo)
	{
		return $this->dropTableName($gdo->gdoTableIdentifier());
	}

	###################
	### DB Creation ###
	###################

	public function dropTableName(string $tableName)
	{
		return self::DBMS()->dbmsDropTable($tableName);
	}

	public function truncateTable(GDO $gdo)
	{
		$tableName = $gdo->gdoTableIdentifier();
		return self::DBMS()->dbmsTruncateTable($tableName);
	}

	public function createDatabase(string $databaseName): void
	{
		self::DBMS()->dbmsCreateDB($databaseName);
	}

	###################
	### Transaction ###
	###################

	public function dropDatabase(string $databaseName): void
	{
		self::DBMS()->dbmsDropDB($databaseName);
	}

	public function useDatabase(string $databaseName): void
	{
		$this->db = $databaseName;
		if ($this->isConnected())
		{
			self::DBMS()->dbmsUseDB($databaseName);
		}
	}

	public function transactionBegin(): void
	{
		self::DBMS()->dbmsBegin();
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

	public function transactionRollback()
	{
		self::DBMS()->dbmsRollback();
	}

	###############
	### FKCheck ###
	###############

	public function lock(string $lock, int $timeout = 30): bool
	{
		$this->locks++; #PP#delete#
		self::$LOCKS++; #PP#delete#
		return self::DBMS()->dbmsLock($lock, $timeout);
	}

	public function unlock(string $lock): bool
	{
		return self::DBMS()->dbmsUnlock($lock);
	}

	##############
	### Import ###
	##############

	/**
	 * Import a large SQL file.
	 */
	public function parseSQLFile(string $path): void
	{
		Database::DBMS()->dbmsExecFile($path);
	}

}
