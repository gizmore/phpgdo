# GDOv7 Cache

There are 3 caches in use.

- Process GDO object cache
- Memcached GDO object cache
- Filecache (pre-rendered output, lang files, etc)

## GDOv7 Cache: Clearing all caches.

If you want to clear all caches, use the [ClearCache Method](../GDO/Admin/Method/ClearCache.php)

    $ gdo_adm.sh clearcache
    # OR
    $ gdo admin.clearcache

## GDOv7 Cache: [Memcached](../GDO/DB/Cache.phpL0)

In case there is no Memcached installed you should set the GDO_MEMCACHE config.php setting to '2'.
In Memcached i store sessions, initialized modules, complete configuration tables like languages and countries and other hot GDO objects.
This is working not too well.
If GDO_MEMCACHE is set to 2, a fallback GDO_FILECACHE is used.
This works great!

## GDOv7 Cache: [Process Cache](../GDO/DB/Cache.php#L168)

All three caches are implemented in the [Cache class](../GDO/DB/Cache.php).
The GDO process cache is caching GDO column structures and ID => GDO mappings for a GDO.
The cash is fresh on each request.
This cache can be populated by the database or an Memcached daemon.

## GDOv7 Cache: [File Cache](../GDO/DB/Cache.php#L415)

This is a simple key => file store in the filesystem.

Of course this does not make sense for tiny values, but a a good usage is for the i18n database.
All that 1000 key => value array is php serialized into a cached file with a lifetime.
