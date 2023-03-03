<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDT;
use GDO\Core\Debug;
use GDO\Core\GDO_DBException;
use GDO\DBMS\Module_DBMS;

/**
 * mySQLi abstraction layer.
 * 
 * @TODO support postgres? This can be achieved via making module DB a separate module. Just need to move some classes to core and ifelse them in creation code?
 * @TODO support sqlite? This can be achieved by a few string tricks maybe. No foreign keys? no idea.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.0
 * 
 * @see GDO
 * @see Query
 * @see Result
 * @see GDT_Table
 */
class Database
{
	# Const
// 	const PRIMARY_USING = 'USING HASH'; # Default index algorithm for primary keys.
	
	# Instance
	private static Database $INSTANCE;
	public static function instance() : self { return self::$INSTANCE; }

	# DBMS
	public static ?Module_DBMS $DBMS = null;
	
	# Connection
	private $link; # any dbms provider link. remove?
	private int $port = 3306; # config
	private string $host, $user, $pass; # config
	private ?string $db; # configured db.
	public  ?string $usedb; # current db in use.

	# Debug
	private int $debug = 0; # Set to 0/off, 1/on, 2/backtraces
	
	# Performance single db
	public int $locks = 0;
	public int $reads = 0;
	public int $writes = 0;
	public int $commits = 0;
	public int $queries = 0;
	public float $queryTime = 0.0;
	
	# Performance summed for all connections
	public static int $LOCKS = 0;
	public static int $READS = 0;
	public static int $WRITES = 0;
	public static int $COMMITS = 0;
	public static int $QUERIES = 0;
	public static float $QUERY_TIME = 0.0;
	
	/**
	 * Available GDO classes.
	 * @var GDO[]
	 */
	private static array $TABLES = [];

	/**
	 * gdoColumns for all GDO.
	 * @var GDT[]
	 */
	private static array $COLUMNS = [];
	
	public static function init(?string $databaseName=GDO_DB_NAME) : self
	{
		Cache::init();
	    return new self(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, $databaseName, intval(GDO_DB_DEBUG));
	}
	
	public function __construct(string $host, string $user, string $pass, string $db = null, int $debug=0)
	{
		self::$INSTANCE = $this;
		$this->debug = $debug;
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
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
	
	public function closeLink() : void
	{
		if (isset($this->link))
		{
			unset($this->link);
			self::$DBMS->dbmsClose();
		}
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
		catch (\Throwable $e)
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
		self::$DBMS = Module_DBMS::instance();
		return self::$DBMS->dbmsOpen($this->host, $this->user, $this->pass, $this->db, $this->port);
	}
	
	#############
	### Query ###
	#############
	public function queryRead(string $query, bool $buffered=true)
	{
		self::$READS++; #PP#delete#
		$this->reads++; #PP#delete#
		return $this->query($query, $buffered);
	}
	
	public function queryWrite($query)
	{
		self::$WRITES++; #PP#delete#
		$this->writes++; #PP#delete#
		return $this->query($query);
	}
	
	private function query(string $query, bool $buffered=true)
	{
// 		try
// 		{
			return $this->queryB($query);
// 		}
// 		catch (\Throwable $ex)
// 		{
// 			throw new GDO_DBException("err_db", [$ex->getCode(), $ex->getMessage(), html($query)]);
// 		}
	}
	
	private function queryB(string $query, bool $buffered=true)
	{
		$t1 = microtime(true); #PP#delete#
		
		$this->getLink();
		
		$result = self::$DBMS->dbmsQuery($query, $buffered);
		
		if (!$result)
		{
			if ($this->link)
			{
				$error = self::$DBMS->dbmsError($this->link);
				$errno = self::$DBMS->dbmsErrno($this->link);
// 				$this->closeLink();
			}
			else
			{
				$error = t('err_db_no_link');
				$errno = 0;
			}
			throw new GDO_DBException("err_db", [$errno, html($error), html($query)]);
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
			Logger::log('queries', "#" . self::$QUERIES .
				": ({$timeTaken}s) ".$query);
			if ($this->debug > 1)
			{
				Logger::log('queries',
					Debug::backtrace('#' . self::$QUERIES . ' Backtrace', false));
			}
		}
		#PP#end#
		
		return $result;
	}
	
	public function insertId() : string
	{
		return self::$DBMS->dbmsInsertId();
	}
	
	public function affectedRows() : int
	{
		return self::$DBMS->dbmsAffected();
	}
	
	###################
	### Table cache ###
	###################
	public static function tableS(string $classname, bool $initCache=true) : ?GDO
	{
		if (!isset(self::$TABLES[$classname]))
		{
		    /** @var $gdo GDO **/
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
	 * Clear cache for all GDO.
	 */
	public static function clearCache() : void
	{
		foreach (self::$TABLES as $gdo)
		{
			$gdo->clearCache();
		}
	}
	
	/**
	 * Extract name from gdo columns for hashmap.
	 * @return GDT[]
	 */
	private static function hashedColumns(GDO $gdo) : array
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
	
	/**
	 * @return GDT[]
	 */
	public static function &columnsS(string $classname) : array
	{
		if (!isset(self::$COLUMNS[$classname]))
		{
    		$gdo = self::tableS($classname);
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);
		}
		return self::$COLUMNS[$classname];
	}
	
	####################
	### Table create ###
	####################
	public function tableExists(string $tableName) : bool
	{
		return Database::$DBMS->dbmsTableExists($tableName);
	}
	
// 	public function createTableCode(GDO $gdo) : string
// 	{
// 		return self::$DBMS->dbmsCreateTableCode($gdo);
// 	}
	
	/**
	 * Create a database table from a GDO. 
	 */
	public function createTable(GDO $gdo) : void
	{
		try
		{
			$this->disableForeignKeyCheck();
			self::$DBMS->dbmsCreateTable($gdo);
		}
		finally
		{
		    $this->enableForeignKeyCheck();
		}
	}
	
	public function dropTable(GDO $gdo)
	{
		return $this->dropTableName($gdo->gdoTableIdentifier());
	}
	
	public function dropTableName(string $tableName)
	{
		return self::$DBMS->dbmsDropTable($tableName);
	}
	
	public function truncateTable(GDO $gdo)
	{
	    $tableName = $gdo->gdoTableIdentifier();
	    return self::$DBMS->dbmsTruncateTable($tableName);
	}
	
	###################
	### DB Creation ###
	###################
	public function createDatabase(string $databaseName): void
	{
		self::$DBMS->dbmsCreateDB($databaseName);
	}
	
	public function dropDatabase(string $databaseName): void
	{
		self::$DBMS->dbmsDropDB($databaseName);
	}
	
	public function useDatabase(string $databaseName): void
	{
		$this->usedb = $databaseName;
		self::$DBMS->dbmsUseDB($databaseName);
	}
	
	###################
	### Transaction ###
	###################
	public function transactionBegin(): void
	{
		$this->getLink();
		self::$DBMS->dbmsBegin();
	}
	
	public function transactionEnd(): void
	{
	    # Perf
		$this->commits++; #PP#delete#
		self::$COMMITS++; #PP#delete#
		
		# Exec and perf
		$t1 = microtime(true); #PP#delete#
		self::$DBMS->dbmsCommit();
		$t2 = microtime(true); #PP#delete#
		$tt = $t2 - $t1; #PP#delete#
		
		# Perf
		$this->queryTime += $tt; #PP#delete#
		self::$QUERY_TIME += $tt; #PP#delete#
	}
	
	public function transactionRollback()
	{
		self::$DBMS->dbmsRollback();
	}
	
	############
	### Lock ###
	############
	public function lock(string $lock, int $timeout=30): void
	{
		$this->locks++; #PP#delete#
		self::$LOCKS++; #PP#delete#
		self::$DBMS->dbmsLock($lock, $timeout);
	}
	
	public function unlock(string $lock): void
	{
		self::$DBMS->dbmsUnlock($lock);
	}
	
	###############
	### FKCheck ###
	###############
	public function enableForeignKeyCheck(bool $bool = true): void
	{
		$this->getLink();
		Database::$DBMS->dbmsForeignKeys($bool);
	}

	public function disableForeignKeyCheck()
	{
		return $this->enableForeignKeyCheck(false);
	}
	
	##############
	### Import ###
	##############
	/**
	 * Import a large SQL file.
	 */
	public function parseSQLFile(string $path) : void
	{
		Database::$DBMS->dbmsExecFile($path);
	}
	
}
