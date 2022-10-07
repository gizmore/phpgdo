<?php
namespace GDO\DB;

use GDO\Core\GDO;
use GDO\Core\GDT_Hook;
use GDO\Util\FileUtil;
use GDO\Core\Module_Core;
use GDO\Core\Application;
use GDO\Core\Logger;
use GDO\Core\Debug;

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
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
class Cache
{
	############
	### Perf ###
	############
	public static int $CACHE_HITS = 0;
	public static int $CACHE_MISSES = 0;
	
	# ################
	# ## Memcached ###
	# ################
	private static \Memcached $MEMCACHED;

	/**
	 * This holds the GDO that need a recache after the method has been executed.
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
	 * Set a memcached item.
	 */
	public static function set(string $key, $value, int $expire = GDO_MEMCACHE_TTL): void
	{
		if (GDO_CACHE_DEBUG)
		{
			self::debug('set', $key, $value);
		}
		
		switch (GDO_MEMCACHE)
		{
			case 1:
				if ( !defined('GDO_MEMCACHED_FALLBACK'))
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
		Logger::log('cache', sprintf('%s %s'));
		if (GDO_CACHE_DEBUG >= 2)
		{
			$isHTML = Application::instance()->isHTML();
			Logger::log('cache', Debug::backtrace('Backtrace', $isHTML));
		}
	}

	public static function replace(string $key, $value, int $expire = GDO_MEMCACHE_TTL)
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				if ( !defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->replace(MEMCACHEPREFIX . $key, $value, $expire);
				}
				break;
			case 2:
				self::fileSetSerialized($key, $value);
				break;
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
				if ( !defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->delete(MEMCACHEPREFIX . $key);
				}
				break;
			case 2:
				self::fileFlush($key);
				break;
		}
	}

	/**
	 * Clear whole cache.
	 */
	public static function flush(): void
	{
		switch (GDO_MEMCACHE)
		{
			case 1:
				if ( !defined('GDO_MEMCACHED_FALLBACK'))
				{
					self::$MEMCACHED->flush();
				}
				break;
			case 2:
				self::fileFlush();
				break;
		}
	}

	public static function init()
	{
		if (GDO_MEMCACHE == 1)
		{
			self::$MEMCACHED = new \Memcached();
			self::$MEMCACHED->addServer(GDO_MEMCACHE_HOST, GDO_MEMCACHE_PORT);
		}
		if ( (GDO_FILECACHE) || (GDO_MEMCACHE == 2) )
		{
			FileUtil::createDir(self::filePath());
		}
	}

	# ########################
	# ## GDO Process Cache ###
	# ########################
	/**
	 * The table object is fine to keep clean?
	 */
	private GDO $table;

	/**
	 * Always have a spare copy for analyzing.
	 * Create a new dummy when a row is fetched as object.
	 */
	private GDO $dummy;

	/**
	 * Full classname
	 */
	private string $klass;

	/**
	 * The single identity GDO cache
	 *
	 * @var GDO[]
	 */
	public array $cache = [];

	public function __construct(GDO $gdo)
	{
		$this->table = $gdo;
		$this->klass = get_class($gdo);
		$this->tableName = strtolower($gdo->gdoShortName());
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

	public function getDummy(): GDO
	{
		return isset($this->dummy) ? $this->dummy : $this->newDummy();
	}

	public function getNewDummy(array $blankVars = null): GDO
	{
		return call_user_func([
			$this->klass,
			'blank'
		], $blankVars);
	}

	private function newDummy(): GDO
	{
		$this->dummy = new $this->klass();
		return $this->dummy;
	}

	/**
	 * Try GDO Cache and Memcached.
	 */
	public function findCached(string ...$ids): ?GDO
	{
		$id = implode(':', $ids);
		if ( !isset($this->cache[$id]))
		{
			if ($mcached = self::get($this->tableName . $id))
			{
				$this->cache[$id] = $mcached;
				self::$CACHE_HITS++;
				return $mcached;
			}
		}
		else
		{
			self::$CACHE_HITS++;
			return $this->cache[$id];
		}
		
		if (isset($this->all))
		{
			foreach ($this->all as $gdo)
			{
				if ($gdo->getID() === $id)
				{
					self::$CACHE_HITS++;
					$this->cache[$id] = $gdo;
					return $gdo;
				}
			}
		}
		
		self::$CACHE_MISSES++;
		return null;
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
					self::$CACHE_HITS++;
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
					self::$CACHE_HITS++;
					return $gdo;
				}
			}
		}
		self::$CACHE_MISSES++;
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
	 * @return GDO
	 */
	public function initCached(array $assoc, bool $useCache = true): GDO
	{
		$this->getDummy()->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if ( !isset($this->cache[$key]))
		{
			$this->cache[$key] = (new $this->klass())->setGDOVars($assoc)->setPersisted();
		}
		elseif ($useCache)
		{
			$this->cache[$key]->setGDOVars($assoc);
		}
		return $this->cache[$key];
	}

	public function clearCache(): void
	{
		unset($this->all);
		$this->cache = [];
		$this->flush();
	}

	public function recache(GDO $object): GDO
	{
		if ( !$object->isPersisted())
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

	public function uncache(GDO $object)
	{
		# Mark for recache
		if (( !isset($object->recache)) && ($object->gdoCached()))
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
	 * memcached + gdo cache initializer
	 */
	public function initGDOMemcached(array $assoc, bool $useCache = true): GDO
	{
		$this->getDummy()->setGDOVars($assoc);
		$key = $this->dummy->getID();
		if ( !isset($this->cache[$key]))
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
			$this->cache[$key] = $mcached;
		}
		elseif ($useCache)
		{
			$this->cache[$key]->setGDOVars($assoc)->setPersisted();
		}
		return $this->cache[$key];
	}

	/**
	 * Check if the parameter is the GDO table object.
	 */
	public function isTable(GDO $gdo): bool
	{
		return $gdo === $this->table;
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

	/**
	 * Check if we have a recent cache for a key.
	 */
	public static function fileHas(string $key, int $expire = GDO_MEMCACHE_TTL): bool
	{
		if ( !GDO_FILECACHE)
		{
			return false;
		}
		$path = self::filePath($key);
		if ( !FileUtil::isFile($path))
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
			self::$CACHE_HITS++;
			$path = self::filePath($key);
			return file_get_contents($path);
		}
		else
		{
			self::$CACHE_MISSES++;
		}
		return null;
	}

	/**
	 * Flush the whole or part of the filecache.
	 */
	public static function fileFlush(string $key = null) : bool
	{
		if ($key === null)
		{
			return FileUtil::removeDir(GDO_TEMP_PATH . 'cache/') && FileUtil::createDir(GDO_TEMP_PATH . 'cache/');
		}
		else
		{
			$filename = self::filePath($key);
			if (FileUtil::isFile($filename))
			{
				return @unlink($filename);
			}
		}
		return true; 
	}

	/**
	 * Get the path of a filecache entry.
	 */
	public static function filePath(string $key = ''): string
	{
		$domain = GDO_DOMAIN;
		$version = Module_Core::GDO_REVISION;
		$key = self::fileKey($key);
		return GDO_TEMP_PATH . "cache/{$domain}_{$version}/{$key}.gdo";
	}

	/**
	 * SanitizeFilename. @TODO Use urlencoding for replaced chars.
	 * 
	 * @param string $key
	 * @return string
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

}
# config :(
deff('GDO_MEMCACHE_FALLBACK', !class_exists('Memcached', false));
deff('MEMCACHEPREFIX', GDO_DOMAIN . Module_Core::GDO_REVISION);
deff('GDO_CACHE_DEBUG', 0);
deff('GDO_FILECACHE', 1);
deff('GDO_MEMCACHE', 2);
deff('GDO_TEMP_PATH', GDO_PATH . (Application::instance()->isUnitTests() ? 'temp_test/' : 'temp/'));
