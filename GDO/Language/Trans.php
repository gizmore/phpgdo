<?php
declare(strict_types=1);
namespace GDO\Language;

use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\Util\FileUtil;

/**
 * Trans - a very cheap, high perfomance, translation API.
 * All data is stored in a single Hashmap to reuse translation data.
 *
 * @version 7.0.3
 * @since 1.0.0
 * @author gizmore
 * @see Cache
 */
final class Trans
{

	final public const CACHE_PREFIX = 'trans_';

	public static string $ISO = GDO_LANGUAGE; # Current ISO

	/**
	 * Base pathes for all translation data files.
	 * @var string[]
	 */
	public static array $PATHS = [];

	/**
	 * Translation data cache.
	 */
	public static array $CACHE = [];


	/**
	 * Number of missing translation keys for stats and testing.
	 */
	public static int $MISS = 0; #PP#delete#


	/**
	 * The keys that are missing in translation.
	 * @var string[]
	 */
	public static array $MISSING = []; #PP#delete#

	/**
	 * Are all pathes added?
	 */
	private static bool $INITED = false;

	/**
	 * Set the current ISO.
	 */
	public static function setISO(?string $iso): void
	{
        if ($iso === null)
        {
            $iso = GDO_LANGUAGE;
        }
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
		self::$PATHS[] = $path;
	}

	/**
	 * Set inited which will laod the i18n on next call.
	 */
	public static function inited(bool $inited = true): void
	{
		self::$INITED = true;
	}

	public static function clearCache(): void
	{
		foreach (GDO_Language::gdoSupportedISOs() as $iso)
		{
			Cache::fileRemove(self::getCacheKey($iso));
		}
	}

	private static function getCacheKey(string $iso): string
	{
		return self::CACHE_PREFIX . $iso;
	}

	/**
	 * Get the cache for an ISO.
	 */
	public static function getCache(string $iso): array
	{
		return self::load($iso);
	}

	/**
	 * Load a translation data into and from cache.
	 */
	public static function load(string $iso): array
	{
		return self::$CACHE[$iso] ?? self::reload($iso);
	}

	private static function reload(string $iso): array
	{
		if (!self::$INITED)
		{
			return [];
		}

		# Try cache
		$cacheKey = self::getCacheKey($iso);
		if (null !== ($cache = Cache::fileGetSerialized($cacheKey)))
		{
			return self::$CACHE[$iso] = $cache;
		}

		self::$CACHE[$iso] = [];
		ModuleLoader::instance()->loadLangFiles();
		foreach (self::$PATHS as $path)
		{
			$pathISO = "{$path}_{$iso}.php";
			if (!FileUtil::isFile($pathISO))
			{
				$en = GDO_LANGUAGE;
				$pathISO = "{$path}_{$en}.php";
			}
			self::$CACHE[$iso] = array_merge(self::$CACHE[$iso], include($pathISO));
		}
		Cache::fileSetSerialized($cacheKey, self::$CACHE[$iso]);
		return self::$CACHE[$iso] ?? GDT::EMPTY_ARRAY;
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
	public static function tiso(?string $iso, string $key, array $args = null): string|array
	{
        $iso = $iso ?: self::$ISO;
		$cache = self::load($iso);
		if (isset($cache[$key]))
		{
			$text = $cache[$key];
			if ($args)
			{
                try
                {
                    $text = vsprintf($text, $args);
                }
                catch (\Throwable $ex)
                {
                    $text = $key .  ': ' . json_encode($args);
                }
            }
		}
		else
		{
			self::missing($iso, $key); #PP#delete#
			$text = "__{$key}";
			if ($args)
			{
				$text .= ': ' . json_encode($args);
			}
		}
		return $text;
	}

	#PP#start#

	/**
	 * When a key is missing, log it.
	 * Optionally removed via PP preprocessor.
	 */
	private static function missing(string $iso, string $key): void
	{
		if (self::$INITED)
		{
			self::$MISS++;
			self::$MISSING[$key] = $key;
		}
	}

	#PP#end#

	/**
	 * Check if a translation key exists.
	 */
	public static function hasKey(string $key): bool
	{
		return self::hasKeyIso(self::$ISO, $key);
	}

	/**
	 * Check if a translation key exists for an ISO.
	 */
	public static function hasKeyIso(string $iso, string $key): bool
	{
		$cache = self::load($iso);
		return isset($cache[$key]);
	}

}

deff('GDO_LANGUAGE', 'en'); #PP#delete#
deff('GDO_FILECACHE', false); #PP#delete#
