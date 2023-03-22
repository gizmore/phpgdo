<?php
namespace GDO\Language;

use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\Util\FileUtil;

/**
 * Trans - a very cheap, high perfomance, translation API.
 * All data is stored in a single Hashmap to reuse translation data.
 *
 * @TODO: Trans: In early loading state errors are handled badly.
 *
 * @version 7.0.3
 * @since 1.0.0
 * @author gizmore
 * @see Cache
 */
final class Trans
{

//	public static bool $FILE_CACHE = false;
	public const CACHE_KEY_PREFIX = 'trans_'; # Current ISO
	public static string $ISO = GDO_LANGUAGE;
	/**
	 * Base pathes for all translation data files.
	 *
	 * @var string[]
	 */
	public static array $PATHS = [];
	/**
	 * Translation data cache.
	 *
	 * @var string[string]
	 */
	public static array $CACHE = [];
	/**
	 * @TODO Shall sitename be appended to seo titles? Implement it? Shall be an option in module UI or Core.
	 * @deprecated
	 */
	public static bool $NO_SITENAME = false;
	/**
	 * Number of missing translation keys for stats and testing.
	 */
	public static int $MISS = 0;
	/**
	 * The keys that are missing in translation.
	 *
	 * @var string[]
	 */
	public static array $MISSING = [];
//	/**
//	 * Is lazy cache loading available for ISO key?
//	 *
//	 * @var bool[]
//	 */
//	private static array $HAS_CACHE = [];
	/**
	 * Are all pathes added? # @TODO: This can be removed? - just install process is ugly?
	 */
	private static bool $INITED = false; #PP#delete#

	/**
	 * Set the current ISO.
	 */
	public static function setISO(string $iso): void
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
	public static function numFiles(): int
	{
		return count(self::$PATHS);
	}

	/**
	 * Add a translation file to the language file pathes.
	 */
	public static function addPath(string $path): void
	{
		self::$PATHS[$path] = $path;
	}

	/**
	 * Set inited and clear cache.
	 *
	 * @TODO separate calls. maybe cache should not be cleared quickly? no idea. Make performance tests for language loading on init.
	 */
	public static function inited(bool $inited = true): void
	{
//	    if (!$inited)
//	    {
////			self::$PATHS = [];
//			self::$CACHE = [];
//			self::$HAS_CACHE = [];
//	    }
		self::$INITED = $inited;
	}

	/**
	 * Get the cache for an ISO.
	 *
	 * @return string[string]
	 */
	public static function getCache(string $iso): array
	{
		return self::load($iso);
	}

	/**
	 * Load a translation data into and from cache.
	 *
	 * @return string[string]
	 */
	public static function load(string $iso): array
	{
		return isset(self::$CACHE[$iso]) ? self::$CACHE[$iso] : self::reload($iso);
	}

	/**
	 * @TODO: This algorithm is bad and i should feel bad.
	 */
	private static function reload(string $iso): array
	{
//		$trans = [];
//		$trans2 = [];

		$cacheKey = self::getCacheKey($iso);
		# Try cache
		if (Cache::fileHas($cacheKey))
		{
			self::$CACHE[$iso] = Cache::fileGetSerialized($cacheKey);
//			self::$HAS_LOADED_FILE_CACHE = true;
			return self::$CACHE[$iso]; # lazy
//		    $content = Cache::fileGetSerialized($key);
//		    self::$CACHE[$iso] = $content;
//		    self::$HAS_LOADED_FILE_CACHE = true;
//		    return self::$CACHE[$iso];
		}

		ModuleLoader::instance()->loadLangFiles();

		# Build lang map
//		if (self::$INITED)
//		{
		self::$CACHE[$iso] = [];
		foreach (self::$PATHS as $path)
		{
			$pathISO = "{$path}_{$iso}.php";
			if (FileUtil::isFile($pathISO))
			{
//				    try
//				    {
//				    }
//				    catch (\Throwable $e)
//				    {
//				        self::$CACHE[$iso] = $trans;
//				        echo GDT_Error::make()->exception($e)->renderHTML();
//				    }
			}
			else
			{
				$pathISO = "{$path}_en.php";
//					$trans2 = include($pathEN);
//					try
//					{
//						if ($t2 = @include($pathEN))
//						{
//							$trans2[] = $t2;
//						}
//					}
//					catch (\Throwable $e)
//					{
//					    self::$CACHE[$iso] = $trans;
//					    echo GDT_Error::responseException($e)->renderHTML();
//					    throw new GDO_Error('err_langfile_corrupt', [$pathEN]);
//					}
			}
			self::$CACHE[$iso] = array_merge(self::$CACHE[$iso], include($pathISO));
		}
//			$trans =
//			$loaded = $trans;
//			$trans = $loaded;

		# Save cache
//    		if (self::$FILE_CACHE)
//    		{
//    		    FileUtil::createDir(Cache::filePath());
//    		    Cache::fileSetSerialized($key, $trans);
//    		}
//		}

//		if (self::$INITED)
//		{
			Cache::fileSetSerialized(self::getCacheKey($iso), self::$CACHE[$iso]);
//		}

		return isset(self::$CACHE[$iso]) ? self::$CACHE[$iso] : GDT::EMPTY_ARRAY;
	}

	private static function getCacheKey(string $iso): string
	{
		return self::CACHE_KEY_PREFIX . $iso;
#		$key = md5("$iso;" . implode(',', self::$PATHS));
		return $key;
	}

	public static function clearCache(): void
	{
		foreach (GDO_Language::gdoSupportedISOs() as $iso)
		{
			Cache::fileRemove(self::getCacheKey($iso));
		}
	}

	/**
	 * Translate into current ISO.
	 */
	public static function t(string $key, array $args = null): string|array
	{
		return self::tiso(self::$ISO, $key, $args);
	}

	/**
	 * Translate key into a language.
	 */
	public static function tiso(string $iso, string $key, array $args = null): string|array
	{
		$cache = self::load($iso);
		if (isset($cache[$key])) # @TODO: aggresive programming - remove the if!
		{
			$text = $cache[$key];
			if ($args)
			{
				if (!($text = @vsprintf($text, $args)))
				{
					self::missing($iso, $key); #PP#delete#
					$text = $cache[$key] . ': ';
					$text .= json_encode($args);
				}
			}
		}
		else # Fallback key + printargs
		{
			self::missing($iso, $key); #PP#delete#
			$text = $key;
			if ($args)
			{
				$text .= ': ' . json_encode($args);
			}
		}
		return $text;
	}

	/**
	 * When a key is missing, log it.
	 * Optionally removed via PP preprocessor.
	 */
	private static function missing(string $iso, string $key, bool $logMissing = false): bool
	{
		#PP#start#
		if (self::$INITED && $logMissing)
		{
			self::$MISS++;
			self::$MISSING[$key] = $key;
			Logger::log("i18n_{$iso}", $key);
		}
		#PP#end#
		return false;
	}

	/**
	 * Check if a translation key exists.
	 */
	public static function hasKey(string $key, bool $logMissing = false): bool
	{
		return self::hasKeyIso(self::$ISO, $key, $logMissing);
	}

	/**
	 * Check if a translation key exists for an ISO.
	 */
	public static function hasKeyIso(string $iso, string $key, bool $logMissing = false): bool
	{
		$cache = self::load($iso);
		return isset($cache[$key]) || self::missing($iso, $key, $logMissing);
	}

}

#############
### Setup ###
#############
deff('GDO_LANGUAGE', 'en'); #PP#delete#
deff('GDO_FILECACHE', false); #PP#delete#
//Trans::$FILE_CACHE = (bool)GDO_FILECACHE;
