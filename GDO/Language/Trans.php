<?php
namespace GDO\Language;

use GDO\Util\FileUtil;
use GDO\UI\GDT_Error;
use GDO\DB\Cache;
use GDO\Core\GDO_Error;
use GDO\Core\Logger;

/**
 * Trans; a very cheap I18n API.
 * All data is stored in a single Hashmap to reuse translation data.
 * 
 * Uses [Filecache](../Core/Cache.php)
 * 
 * @TODO: Trans: Check if ini file parsing or other techniques would be faster than php include to populate the hashmap.
 * @TODO: Trans: In early loading state errors are handled badly.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 1.0.0
 * @see Cache
 */
final class Trans
{
	public static bool $FILE_CACHE = false;
	public static string $ISO = GDO_LANGUAGE;

	private static bool $HAS_LOADED_FILE_CACHE = false;
	
	/**
	 * Base pathes for translation data files.
	 * @var string[]
	 */
	public static array $PATHS = [];
	
	/**
	 * Translation data cache.
	 * @var string[string]
	 */
	public static array $CACHE = [];
	
	/**
	 * Are all pathes added? # @TODO: This can be removed?
	 */
	private static bool $INITED = false;
	
	/**
	 * @TODO Shall sitename be appended to seo titles? Implement it? Shall be an option in module UI or Core.
	 */
	public static bool $NO_SITENAME = false;
	
	/**
	 * Number of missing translation keys for stats and testing.
	 */
	public static int $MISS = 0;
	
	/**
	 * The keys that are missing in translation.
	 * @var string[]
	 */
	public static array $MISSING = [];
	
	/**
	 * Set the current ISO.
	 */
	public static function setISO(string $iso) : void
	{
	    if ($iso !== self::$ISO)
	    {
    		# Set Trans locale
    		self::$ISO = $iso;
    		# Generate utf8 locale identifier, e.g: de_DE.utf8
    		$iso = $iso . '_' . strtoupper($iso) . '.utf-8';
   			setlocale(LC_TIME, $iso); # Bug... sometimes it needs two calls?!
	    }
	}
	
	/**
	 * Show number of registered translation data base pathes.
	 * In case we used the filecache this is set to 1.
	 */
	public static function numFiles() : int
	{
	    if (self::$HAS_LOADED_FILE_CACHE)
	    {
	        return 1;
	    }
		return count(self::$PATHS);
	}

	/**
	 * Add a translation file to the language file pathes.
	 */
	public static function addPath(string $path) : void
	{
	    self::$PATHS[] = $path;
	}
	
	/**
	 * Set inited and clear cache.
	 * @TODO separate calls. maybe cache should not be cleared quickly? no idea. Make performance tests for language loading on init.
	 */
	public static function inited(bool $inited = true) : void
	{
// 	    if (!$inited)
// 	    {
	    	self::$CACHE = [];
// 	    }
		self::$INITED = $inited;
	}
	
	/**
	 * Get the cache for an ISO.
	 * @return string[string]
	 */
	public static function getCache(string $iso) : array
	{
		return self::load($iso);
	}
	
	/**
	 * Load a translation data into and from cache.
	 * @return string[string]
	 */
	public static function &load(string $iso) : array
	{
		if (!isset(self::$CACHE[$iso]))
		{
			return self::reload($iso);
		}
		return self::$CACHE[$iso];
	}
	
	/**
	 * Translate into current ISO.
	 * @return string|string[]
	 */
	public static function t(string $key, array $args=null)
	{
		return self::tiso(self::$ISO, $key, $args);
	}
	
	/**
	 * Translate key into a language.
	 * @return string|string[]
	 */
	public static function tiso(string $iso, string $key, array $args=null)
	{
		$cache = self::load($iso);
		if (isset($cache[$key]))
		{
		    $text = $cache[$key];
			if ($args)
			{
				if (!($text = @vsprintf($text, $args)))
				{
					self::missing($iso, $key);
					$text = $cache[$key] . ': ';
					$text .= json_encode($args);
				}
			}
		}
		else # Fallback key + printargs
		{
			self::missing($iso, $key);
		    $text = $key;
			if ($args)
			{
				$text .= ': ';
				$text .= json_encode($args);
			}
		}
		return $text;
	}

	/**
	 * When a key is missing, log it.
	 */
	private static function missing(string $iso, string $key, bool $logMissing=false) : bool
	{
		if (self::$INITED && $logMissing)
		{
			self::$MISS++;
			self::$MISSING[$key] = $key;
			Logger::log("i18n_{$iso}", $key);
		}
		return false;
	}

	private static function getCacheKey(string $iso) : string
	{
		$key = md5("$iso;" . implode(',', self::$PATHS));
		return $key;
	}
	
	/**
	 * @TODO: This algorithm is bad and i should feel bad.
	 */
	private static function &reload(string $iso) : array
	{
		$trans = [];
		$trans2 = [];
		
		# Try cache
		$key = self::getCacheKey($iso);
		if (self::$FILE_CACHE && Cache::fileHas($key))
		{
		    $content = Cache::fileGetSerialized($key);
		    self::$CACHE[$iso] = $content;
		    self::$HAS_LOADED_FILE_CACHE = true;
		    return self::$CACHE[$iso];
		}
		
		# Build lang map
// 		if (self::$INITED)
		{
			foreach (self::$PATHS as $path)
			{
			    $pathISO = "{$path}_{$iso}.php";
				if (FileUtil::isFile($pathISO))
				{
				    try
				    {
						$trans2[] = include($pathISO);
				    }
				    catch (\Throwable $e)
				    {
				        self::$CACHE[$iso] = $trans;
				        echo GDT_Error::make()->exception($e)->renderHTML();
				    }
				}
				else
				{
				    $pathEN= "{$path}_en.php";
					try
					{
						if ($t2 = @include($pathEN))
						{
							$trans2[] = $t2;
						}
					}
					catch (\Throwable $e)
					{
					    self::$CACHE[$iso] = $trans;
					    echo GDT_Error::responseException($e)->renderHTML();
					    throw new GDO_Error('err_langfile_corrupt', [$pathEN]);
					}
				}
			}
			$trans = array_merge(...$trans2);
			$loaded = $trans;
			$trans = $loaded;
    		self::$CACHE[$iso] = $trans;
    		
    		# Save cache
    		if (self::$FILE_CACHE)
    		{
    		    FileUtil::createDir(Cache::filePath());
    		    Cache::fileSetSerialized($key, $trans);
    		}
		}
		
		return $trans;
	}
	
	/**
	 * Check if a translation key exists.
	 */
	public static function hasKey(string $key, bool $logMissing=false) : bool
	{
	    return self::hasKeyIso(self::$ISO, $key, $logMissing);
	}

	/**
	 * Check if a translation key exists for an ISO.
	 */
	public static function hasKeyIso(string $iso, string $key, bool $logMissing=false) : bool
	{
		$cache = self::load($iso);
		return isset($cache[$key]) ? true : self::missing($iso, $key, $logMissing);
	}
	
}
#############
### Setup ###
#############
deff('GDO_LANGUAGE', 'en');
deff('GDO_FILECACHE', false);
Trans::$FILE_CACHE = (bool) GDO_FILECACHE;
