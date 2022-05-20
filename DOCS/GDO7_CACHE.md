# GDOv7 Cache

There are 3 caches in use.

 - Process GDO object cache
 - Memcached GDO object cache
 - Filecache (pre-rendered output, lang files, etc)

If you want to clear all caches, use the [ClearCache Method](..GDO/Admin/Method/ClearCache.php)

    ClearCache::make()->clearCache();


## GDOv7 Cache: GDO Process cache

All three caches are implemented in the [Cache class](../GDO/DB/Cache.php).
The GDO process cache is caching GDO column structures and ID => GDO mappings for a cache.
 The cash is fresh on each request.


## GDOv7 [Memcached](../GDO/DB/Cache.php)


## GDOv7 [File Cache](../GDO/DB/Cache.php)
