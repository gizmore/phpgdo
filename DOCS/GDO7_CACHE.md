# GDOv7 Cache

There are 3 caches in use.

 - Process GDO object cache
 - Memcached GDO object cache
 - Filecache (pre-rendered output, lang files, etc)


## GDOv7 Cache: Clearing all caches.

If you want to clear all caches, use the [ClearCache Method](../GDO/Admin/Method/ClearCache.php)

    ClearCache::make()->clearCache();


## GDOv7 Cache: [Memcached](../GDO/DB/Cache.phpL0)

In case there is no Memcached installed you should set this config.php setting to false.
In Memcached i store sessions, initialized modules, complete configuration tables like languages and countries and other hot GDO objects.
It does not speed up much though.


## GDOv7 [Process Cache](../GDO/DB/Cache.php#L100)

All three caches are implemented in the [Cache class](../GDO/DB/Cache.php).
The GDO process cache is caching GDO column structures and ID => GDO mappings for a GDO.
 The cash is fresh on each request.
 This cache can be populated by the database or an  Memcached daemon.
 

## GDOv7 [File Cache](../GDO/DB/Cache.php#L300)

This is a simple key => file store in the filesystem.

Of course this does not make sense for tiny values, but a a good usage is for the i18n database.
All that 1000 key => value array is php serialized into a cached file with a lifetime.
