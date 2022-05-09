<?php
namespace GDO\DB;

use mysqli;
use mysqli_result;
use GDO\Core\GDO;
use GDO\Core\Logger;
use GDO\Core\GDT;
use GDO\Core\Debug;
use GDO\Core\GDO_DBException;

/**
 * mySQLi abstraction layer.
 * 
 * @TODO support postgres? This can be achieved via making module DB a separate module. Just need to move some classes to core and ifelse them in creation code?
 * @TODO support sqlite? This can be achieved by a few string tricks maybe. No foreign keys? no idea.
 * 
 * @author gizmore
 * @version 7.0.0
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
	const PRIMARY_USING = 'USING HASH'; # Default index algorithm for primary keys.
	
	# Instance
	private static Database $INSTANCE;
	public static function instance() : self { return self::$INSTANCE; }
	
	# Connection
	private mysqli $link;
	private string $host, $user, $pass, $db;

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
	 * Available GDO classes
	 * 
	 * @var GDO[]
	 */
	private static array $TABLES = [];

	/**
	 * gdoColumns for all GDO
	 * @var GDT[]
	 */
	private static array $COLUMNS = [];
	
	public static function init() : self
	{
		Cache::init();
	    return new self(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, GDO_DB_NAME, GDO_DB_DEBUG);
	}
	
	public function __construct(string $host, string $user, string $pass, string $db, int $debug=0)
	{
		self::$INSTANCE = $this;
		$this->debug = $debug;
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	public function __destruct()
	{
		$this->closeLink();
	}
	
	public function closeLink() : void
	{
		if (isset($this->link))
		{
			@mysqli_close($this->link);
			$this->link = null;
		}
	}
	
	public function getLink() : mysqli
	{
		return $this->link ? $this->link : $this->openLink();
	}
	
	private function openLink() : mysqli
	{
		try
		{
			$t1 = microtime(true);
			if ($this->link = $this->connect())
			{
				# This is more like a read because nothing is written to the disk.
				$this->queryRead("SET NAMES UTF8");
				$this->queryRead("SET time_zone = '+00:00'");
				return $this->link;
			}
		}
		catch (\Throwable $e)
		{
			throw new GDO_DBException('err_db_connect', [$e->getMessage()]);
		}
		finally
		{
			$timeTaken = microtime(true) - $t1;
			$this->queryTime += $timeTaken;
			self::$QUERY_TIME += $timeTaken;
		}
	}
	
	public function connect() : mysqli
	{
		return mysqli_connect($this->host, $this->user, $this->pass, $this->db);
	}
	
	#############
	### Query ###
	#############
	public function queryRead(string $query, bool $buffered=true) : mysqli_result
	{
		self::$READS++;
		$this->reads++;
		return $this->query($query, $buffered);
	}
	
	public function queryWrite($query) : mysqli_result
	{
		self::$WRITES++;
		$this->writes++;
		return $this->query($query);
	}
	
	private function query(string $query, bool $buffered=true) : mysqli_result
	{
		$t1 = microtime(true);
		
		if ($buffered)
		{
		    $result = mysqli_query($this->getLink(), $query);
		}
		else 
		{
			$result = false;
		    if (mysqli_real_query($this->getLink(), $query))
		    {
		        $result = mysqli_use_result($this->getLink());
		    }
		}
		
		if (!$result)
		{
			if ($this->link)
			{
				$error = @mysqli_error($this->link);
				$errno = @mysqli_errno($this->link);
				$this->closeLink();
			}
			else
			{
				$error = t('err_db_no_link');
				$errno = 0;
			}
			throw new GDO_DBException("err_db", [$errno, $error, $query]);
		}
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
		return $result;
	}
	
	public function insertId() : string
	{
		return (string) mysqli_insert_id($this->getLink());
	}
	
	public function affectedRows() : int
	{
		return mysqli_affected_rows($this->getLink());
	}
	
	###################
	### Table cache ###
	###################
	/**
	 * @param string $classname
	 * @return GDO
	 */
	public static function tableS(string $classname, bool $initCache=true) : GDO
	{
		if (!isset(self::$TABLES[$classname]))
		{
		    /** @var $gdo GDO **/
		    self::$TABLES[$classname] = $gdo = new $classname();
			$gdo->isTable = true;
			
			if ($gdo->gdoAbstract())
			{
				return null;
			}
			
			self::$COLUMNS[$classname] = self::hashedColumns($gdo);

		    # Always init a cache item.
			$gdo->initCache();
			
			$gdo->setInited();
		}
		return self::$TABLES[$classname];
	}
	
	/**
	 * Extract name from gdo columns for hashmap.
	 * @param GDT[] $gdoColumns
	 * @return GDT[]
	 */
	private static function hashedColumns(GDO $gdo) : array
	{
		$columns = [];
		foreach ($gdo->gdoColumns() as $gdt)
		{
			$columns[$gdt->getName()] = $gdt; # FIXME #->gdtTable($gdo);
		}
		return $columns;
	}
	
	/**
	 * @param string $classname
	 * @return GDT[]
	 */
	public static function &columnsS($classname) : array
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
	public function createTableCode(GDO $gdo) : string
	{
		$columns = [];
		$primary = [];
		
		foreach ($gdo->gdoColumnsCache() as $column)
		{
			if ($define = $column->gdoColumnDefine())
			{
				$columns[] = $define;
			}
			if ($column->primary)
			{
				$primary[] = $column->identifier();
			}
		}
		
		if (count($primary))
		{
			$primary = implode(',', $primary);
			$columns[] = "PRIMARY KEY ($primary) " . self::PRIMARY_USING;
		}
		
		foreach ($gdo->gdoColumnsCache() as $column)
		{
			if ($column->unique)
			{
				$columns[] = "UNIQUE({$column->identifier()})";
			}
		}
		
		$columnsCode = implode(",\n", $columns);
		
		$query = "CREATE TABLE IF NOT EXISTS {$gdo->gdoTableIdentifier()} ".
		         "(\n$columnsCode\n) ENGINE = {$gdo->gdoEngine()}";
		
		return $query;
	}
	
	/**
	 * Create a database table from a GDO. 
	 * @param GDO $gdo
	 * @return bool
	 */
	public function createTable(GDO $gdo) : bool
	{
		try
		{
		    $this->disableForeignKeyCheck();
    		$query = $this->createTableCode($gdo);
    		$this->queryWrite($query);
    		return true;
		}
		catch (\Throwable $ex)
		{
		    throw $ex;
		}
		finally
		{
		    $this->enableForeignKeyCheck();
		}
	}
	
	public function dropTable(GDO $gdo) : mysqli_result
	{
	    $tableName = $gdo->gdoTableIdentifier();
		return $this->queryWrite("DROP TABLE IF EXISTS {$tableName}");
	}
	
	public function truncateTable(GDO $gdo) : mysqli_result
	{
	    $tableName = $gdo->gdoTableIdentifier();
	    return $this->queryWrite("TRUNCATE TABLE {$tableName}");
	}
	
	###################
	### DB Creation ###
	###################
	public function createDatabase($databaseName) : mysqli_result
	{
		return $this->queryWrite("CREATE DATABASE $databaseName");
	}
	
	public function dropDatabase($databaseName) : mysqli_result
	{
		return $this->queryWrite("DROP DATABASE $databaseName");
	}
	
	public function useDatabase($databaseName) : mysqli_result
	{
	    $this->queryRead("USE $databaseName");
	}
	
	###################
	### Transaction ###
	###################
	public function transactionBegin() : mysqli_result
	{
		return mysqli_begin_transaction($this->getLink());
	}
	
	public function transactionEnd() : mysqli_result
	{
	    # Perf
		$this->commits++;
		self::$COMMITS++;
		
		# Exec and perf
		$t1 = microtime(true);
		$result = mysqli_commit($this->getLink());
		$t2 = microtime(true);
		$tt = $t2 - $t1;
		
		# Perf
		$this->queryTime += $tt;
		self::$QUERY_TIME += $tt;
		return $result;
	}
	
	public function transactionRollback()
	{
		return mysqli_rollback($this->getLink());
	}
	
	############
	### Lock ###
	############
	public function lock(string $lock, int $timeout=30) : mysqli_result
	{
	    $this->locks++;
	    self::$LOCKS++;
		$query = "SELECT GET_LOCK('{$lock}', {$timeout}) as L";
		return $this->queryRead($query);
	}
	
	public function unlock($lock) : mysqli_result
	{
		$query = "SELECT RELEASE_LOCK('{$lock}') as L";
		return $this->queryRead($query);
	}
	
	###############
	### FKCheck ###
	###############
	public function enableForeignKeyCheck(bool $bool) : mysqli
	{
		$check = $bool ? '1' : '0';
		return $this->query("SET foreign_key_checks = $check");
	}

	public function disableForeignKeyCheck() : mysqli
	{
		return $this->enableForeignKeyCheck(false);
	}
	
	##############
	### Import ###
	##############
	/**
	 * Import a large SQL file.
	 * 
	 * @param string $path
	 */
	public function parseSQLFile(string $path) : bool
	{
	    $fh = fopen($path, 'r');
	    $command = '';
	    while ($line = fgets($fh))
	    {
	        if ( (str_starts_with($line, '-- ')) ||
	             (str_starts_with($line, '/*')) )
	        {
	            # skip comments
	            continue;
	        }
	        
	        # Append to command
	        $command .= $line;
	        
	        # Finished command
	        if (str_ends_with(trim($line), ';'))
	        {
	            # Most likely a write
    	        $this->queryWrite($command);
    	        $command = '';
	        }
	    }
	    return true;
	}
	
}
