<?php
namespace GDO\DB;

use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO;
use GDO\Core\GDO_Error;
use GDO\Core\GDT_Hook;
use GDO\Core\Logger;
use GDO\Core\Module_Core;
use GDO\Util\FileUtil;
use Memcached;

/**
 * Cache is a global object cache, where each fetched object (with the same key) from the database results in the same instance.
 * This way you can never have two dangling out of sync users in your application.
 * It also saves a bit mem.
 * Of course this comes with a slight overhead.
 * As GDOv7 was written from scratch with this in mind, the overhead is quite small.
 *
 * Suprising is the additional use of memcached (did not plan this) which adds a second layer of caching.
 *
 * There are a few global memcached keys scattered across the application, fetching all rows or similiar stuff.
 * Those GDOs usually dont use memcached on a per row basis and gdoMemcached is false.
 *
 * gdo_modules
 * gdo_country
 * gdo_language
 *
 * The other memcached keys work on a per row basis with table_name_id as key.
 *
 * @version 7.0.2
 * @since 5.0.0
 * @author gizmore
 */
class Cache
{

	############
	### Perf ###
	############
	# file-cache performance stats
	public static int $CACHE_HITS = 0;
	public static int $CACHE_MISSES = 0;
	public static int $CACHE_REMOVE = 0;
	public static int $CACHE_FLUSH = 0;

	# temp-cache performance stats
	public static int $TEMP_READ = 0;
	public static int $TEMP_CACHE = 0;
	public static int $TEMP_WRITE = 0;
	public static int $TEMP_CLEAR = 0;
	public static int $TEMP_CLEAR_ALL = 0;

	# ################
	# ## Memcached ###
	# ################
	private static Memcached $MEMCACHED;

	/**
	 * This holds the GDO that need a recache after the method has been executed.
	 *
	 * @var GDO[]
	 */
	private static array $RECACHING = [];

	# Primary Key Column Names
	public array $pkNames;

	# Primary Key Columns
	public array $pkColumns;

	# Cached transformed table name
	public string $tableName;


	# ################
	# ## Memcached ###
	# ################
	/**
	 *
	 * @var GDO[] All rows.
	 * @see GDO::allCached()
	 */
	public array $all;

	/**
	 * Expire time in seconds for allCached().
	 */
	public int $allExpire;
	/**
	 * The single identity GDO cache
	 *
	 * @var GDO[]
	 */
	public array $cache = [];
	/**
	 * The table object is fine to keep clean?
	 */
	private GDO $table;

	#PP#start#
	/**
	 * Always have a spare copy for analyzing.
	 * Create a new dummy when a row is fetched as object.
	 */
	private GDO $dummy;

	#PP#end#
	/**
	 * Full classname
	 */
	private string $klass;

	public function __construct(GDO $gdo)
	{
		$this->table = $gdo;
		$this->klass = get_class($gdo);
		$this->tableName = strtolower($gdo->gdoShortName());
	}

	public static function init()
	{
		if (GDO_MEMCACHE == 1)
		{
			self::$MEMCACHED = new Memcached();
			self::$MEMCACHED->addServer(GDO_MEMCACHE_HOST, GDO_MEMCACHE_PORT);
		}
		if ((GDO_FILECACHE) || (GDO_MEMCACHE == 2))
		{
			FileUtil::createDir(self::filePath());
		}
	}

	/**
	 * Get the path of a filecache entry.
	 */
	public static function filePath(string $key = ''): string
	{
		$domain = GDO_DOMAIN;
		$version = Module_Core::GDO_REVISION;
		$key = $key ? self::fileKey($key) . '.gdo' : '';
		return GDO_TEMP_PATH . "cache/{$domain}_{$version}/{$key}";
	}

	# ########################
	# ## GDO Process Cache ###
	# ########################

	/**
	 * SanitizeFilename. @TODO Use urlencoding for replaced chars.
	 */
	private static function fileKey(string $key): string
	{
		return str_replace([
			'"',
			'/',
			'<',
			'>',
			'?',
			':',
		],
			'_',
			$key);
	}

	public static function recacheHooks(): void
	{
		if (GDO_IPC && Application::$INSTANCE->isWebServer())
		{
			foreach (self::$RECACHING as $gdo)
			{
				GDT_Hook::callWithIPC('CacheInvalidate', $gdo->table()->cache->klass, $gdo->getID());
			}
		}
	}

	public function getNewDummy(array $blankVars = null): GDO
	{
		return call_user_func([
			$this->klass,
			'blank',
		], $blankVars);
	}

	/**
	 * Try GDO Cache and Memcached.
	 */
	public function findCached(string ...$ids): ?GDO
	{
		$id = implode(':', $ids);
		if (!isset($this->cache[$id]))
		{
			if ($mcached = self::get($this->tableName . $id))
			{
				$this->cache[$id] = $mcached;
				self::$CACHE_HITS++; #PP#delete#
				return $mcached;
			}
		}
		else
		{
			self::$CACHE_HITS++; #PP#delete#
			return $this->cache[$id];
		}

		if (isset($this->all))
		{
			foreach ($this->all as $gdo)
			{
				if ($gdo->getID() === $id)
				{
					self::$CACHE_HITS++; #PP#delete#
					$this->cache[$id] = $gdo;
					return $gdo;
				}
			}
		}

		self::$CACHE_MISSES++; #PP#delete#
		return null;
	}

	public static function get(string $key)
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				return defined('GDO_MEMCACHED_FALLBACK') ? null : self::$MEMCACHED->get(MEMCACHEPREFIX . $key);
			case 2:
				return self::fileGetSerialized($key);
		}
	}

	/**
	 * Get a value from file cache and de-serialize.
	 */
	public static function fileGetSerialized(string $key, int $expire = GDO_MEMCACHE_TTL)
	{
		if ($str = self::fileGet($key, $expire))
		{
			return unserialize($str);
		}
		return null;
	}

	/**
	 * Get cached content from the file system.
	 */
	public static function fileGet(string $key, int $expire = GDO_MEMCACHE_TTL): ?string
	{
		if (self::fileHas($key, $expire))
		{
			self::$CACHE_HITS++; #PP#delete#
			$path = self::filePath($key);
			return file_get_contents($path);
		}
		else
		{
			self::$CACHE_MISSES++; #PP#delete#
		}
		return null;
	}

	/**
	 * Check if we have a recent cache for a key.
	 */
	public static function fileHas(string $key, int $expire = GDO_MEMCACHE_TTL): bool
	{
		if (!GDO_FILECACHE)
		{
			return false;
		}
		$path = self::filePath($key);
		if (!FileUtil::isFile($path))
		{
			return false;
		}
		$time = filemtime($path);
		if ((Application::$TIME - $time) > $expire)
		{
			unlink($path);
			return false;
		}
		return true;
	}

	/**
	 * Try GDO ALL Cache for getBy().
	 * Try GDO Process Cache for getBy().
	 *
	 * @since 7.0.1
	 */
	public function getCachedBy(string $key, string $var): ?GDO
	{
		if (isset($this->all))
		{
			foreach ($this->all as $gdo)
			{
				if ($gdo->gdoVar($key) === $var)
				{
					self::$CACHE_HITS++; #PP#delete#
					return $gdo;
				}
			}
		}
		if (isset($this->cache))
		{
			foreach ($this->cache as $gdo)
			{
				if ($gdo->gdoVar($key) === $var)
				{
					self::$CACHE_HITS++; #PP#delete#
					return $gdo;
				}
			}
		}
		self::$CACHE_MISSES++; #PP#delete#
		return null;
	}

	public function hasID(string $id): bool
	{
		return isset($this->cache[$id]);
	}

	/**
	 * Only GDO Cache / No memcached initializer.
	 *
	 * @param array $assoc
	 *
	 * @return GDO
	 */
	public function initCached(array $assoc, bool $useCache = true): GDO
	{
		$this->getDummy()->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if (!isset($this->cache[$key]))
		{
			$this->cache[$key] = (new $this->klass())->setGDOVars($assoc)->setPersisted();
		}
		else #if ($useCache)
		{
			$this->cache[$key]->setGDOVars($assoc);
		}
		return $this->cache[$key];
	}

	public function getDummy(): GDO
	{
		return isset($this->dummy) ? $this->dummy : $this->newDummy();
	}

	private function newDummy(): GDO
	{
		$this->dummy = new $this->klass();
		return $this->dummy;
	}

	public function clearCache(): void
	{
		unset($this->all);
		$this->cache = [];
		$this->flush();
	}

	/**
	 * Clear whole cache.
	 */
	public static function flush(): void
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				if (!defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->flush();
				}
				break;
			case 2:
				self::fileFlush();
				break;
		}
	}

	/**
	 * Remove the whole filecache.
	 */
	public static function fileFlush(string $key = null): bool
	{
		self::$CACHE_FLUSH++; #PP#delete#
		return FileUtil::removeDir(GDO_TEMP_PATH . 'cache/')
			&& FileUtil::createDir(GDO_TEMP_PATH . 'cache/');
	}

	public function recache(GDO $object): GDO
	{
		if (!$object->isPersisted())
		{
			return $object;
		}

		$back = $object;

		# GDO cache
		if ($back->gdoCached())
		{
			$id = $object->getID();

			# GDO single cache
			if (isset($this->cache[$id]))
			{
				$old = $this->cache[$id];
				$old->setGDOVars($object->getGDOVars());
				$back = $old;
			}
			else
			{
				$this->cache[$id] = $back;
			}
		}

		# Memcached
		if (GDO_MEMCACHE && $back->memCached())
		{
			self::replace($back->gkey(), $back, GDO_MEMCACHE_TTL);
		}

		# Mark for recache
		if ($back->gdoCached())
		{
			if (isset($back->recache))
			{
				self::$RECACHING[] = $back->recaching();
			}
		}

// 		$back->tempReset();

		return $back;
	}

	public static function replace(string $key, $value, int $expire = GDO_MEMCACHE_TTL)
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				if (!defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->replace(MEMCACHEPREFIX . $key, $value, $expire);
				}
				break;
			case 2:
				self::fileSetSerialized($key, $value);
				break;
		}
	}

	# #################
	# ## File cache ###
	# #################

	/**
	 * Store an item in a file cash.
	 * You can use self::fileSet() instead, if you only want to cache a string.
	 */
	public static function fileSetSerialized(string $key, $value): bool
	{
		if (GDO_FILECACHE)
		{
			$content = serialize($value);
			return self::fileSet($key, $content);
		}
		return false;
	}

	/**
	 * Put cached content on the file system.
	 */
	public static function fileSet(string $key, string $content): bool
	{
		if (GDO_FILECACHE)
		{
			$path = self::filePath($key);
			FileUtil::createDir(dirname($path));
			return file_put_contents($path, $content) !== false;
		}
		return false;
	}

	public function uncache(GDO $object)
	{
		# Mark for recache
		if ((!isset($object->recache)) && ($object->gdoCached()))
		{
			self::$RECACHING[] = $object->recaching();
		}

		$id = $object->getID();
		unset($this->cache[$id]);

		if (GDO_MEMCACHE && $object->memCached())
		{
			self::remove($object->gkey());
		}
	}

	/**
	 * Remove a single memcached variable.
	 */
	public static function remove(string $key): void
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				if (!defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->delete(MEMCACHEPREFIX . $key);
				}
				break;
			case 2:
				self::fileRemove($key);
				break;
		}
	}

	/**
	 * Remove a file from filecache if it exists.
	 *
	 * @throws GDO_Error
	 */
	public static function fileRemove(string $key = null): bool
	{
		$filename = self::filePath($key);
		if (FileUtil::isFile($filename))
		{
			self::$CACHE_REMOVE++; #PP#delete#
			return FileUtil::removeFile($filename);
		}
		return true;
	}

	/**
	 * memcached + gdo cache initializer
	 */
	public function initGDOMemcached(array $assoc, bool $useCache = true): GDO
	{
		$this->getDummy()->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if (!isset($this->cache[$key]))
		{
			$gkey = $this->dummy->gkey();
			if (null === ($mcached = self::get($gkey)))
			{
				$mcached = $this->dummy->setGDOVars($assoc)->setPersisted();
				if (GDO_MEMCACHE)
				{
					self::set($gkey, $mcached, GDO_MEMCACHE_TTL);
				}
				$this->newDummy();
			}
			else
			{
				$mcached->setGDOVars($assoc)->setPersisted();
			}
			$this->cache[$key] = $mcached;
		}
		else #if ($useCache)
		{
			$this->cache[$key]->setGDOVars($assoc)->setPersisted();
		}
		return $this->cache[$key];
	}

	/**
	 * Set a memcached item.
	 */
	public static function set(string $key, $value, int $expire = GDO_MEMCACHE_TTL): void
	{
		#PP#start#
		if (GDO_CACHE_DEBUG)
		{
			self::debug('set', $key, $value);
		}
		#PP#end#

		switch (GDO_MEMCACHE)
		{
			case 1:
				if (!defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->set(MEMCACHEPREFIX . $key, $value, $expire);
				}
				break;
			case 2:
				self::fileSetSerialized($key, $value);
				break;
		}
	}

	private static function debug(string $event, string $key, $value)
	{
		Logger::log('cache', sprintf('%s %s', $event, $key));
		if (GDO_CACHE_DEBUG >= 2)
		{
			$isHTML = Application::instance()->isHTML();
			Logger::log('cache', Debug::backtrace('Backtrace', $isHTML));
		}
	}

	/**
	 * Check if the parameter is the GDO table object.
	 */
	public function isTable(GDO $gdo): bool
	{
		return $gdo === $this->table;
	}

}

# config
define('GDO_MEMCACHE_FALLBACK', !class_exists('Memcached', false));
define('MEMCACHEPREFIX', GDO_DOMAIN . Module_Core::GDO_REVISION);
define('GDO_TEMP_PATH', GDO_PATH . (Application::instance()->isUnitTests() ? 'temp_test/' : 'temp/'));
#PP#start#
deff('GDO_FILECACHE', 1);   # enable filecache
deff('GDO_MEMCACHE', 2);    # fallback to filecache
deff('GDO_CACHE_DEBUG', 0); # off
#PP#end#
