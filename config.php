<?php
################################
### GDOv7 Configuration File ###
################################
if (defined('GDO_CONFIGURED')) return; // double include

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Please work down each section carefully.
 * Common pitfall is that there are 2 domains to set: GDO_DOMAIN and GDO_SESS_DOMAIN.
 * phpGDOv7.0.1-r1559 **/
define('GDO_CONFIGURED', '1');

############
### Site ###
############
define('GDO_SITENAME', 'GDOv7');
define('GDO_SEO_URLS', true);
define('GDO_SITECREATED', '2022-09-01 02:44:06.559');
define('GDO_LANGUAGE', 'en');
define('GDO_TIMEZONE', 'UTC');
define('GDO_THEMES', 'default');
define('GDO_MODULE', 'Core');
define('GDO_METHOD', 'Welcome');
define('GDO_IPC', null);
define('GDO_IPC_DEBUG', false);
define('GDO_GDT_DEBUG', 0);
define('GDO_JSON_DEBUG', true);

############
### HTTP ###
############
define('GDO_DOMAIN', 'localhost');
define('GDO_SERVER', 'apache2.4');
define('GDO_PROTOCOL', 'http');
define('GDO_WEB_ROOT', '/phpgdo/');

#############
### Files ###
#############
define('GDO_FILES_DIR', 'files');
define('GDO_CHMOD', 0770);

###############
### Logging ###
###############
define('GDO_LOG_REQUEST', true);
define('GDO_ERROR_LEVEL', 0x37ff);
define('GDO_ERROR_STACKTRACE', true);
define('GDO_ERROR_DIE', true);
define('GDO_ERROR_MAIL', true);

################
### Database ###
################
define('GDO_SALT', 'jShP1OLfgiZpE0uh');
define('GDO_DB_ENABLED', true);
define('GDO_DB_HOST', 'localhost');
define('GDO_DB_USER', 'gdo7');
define('GDO_DB_PASS', 'gdo7');
define('GDO_DB_NAME', 'gdo7');
define('GDO_DB_ENGINE', null);
define('GDO_DB_DEBUG', 1);

#############
### Cache ###
#############
define('GDO_CACHE_DEBUG', 0);
define('GDO_FILECACHE', null);
define('GDO_MEMCACHE', 0);
define('GDO_MEMCACHE_HOST', '127.0.0.1');
define('GDO_MEMCACHE_PORT', 61221);
define('GDO_MEMCACHE_TTL', 1800);

###############
### Cookies ###
###############
define('GDO_SESS_NAME', 'GDO7');
define('GDO_SESS_DOMAIN', 'localhost');
define('GDO_SESS_TIME', 604800);
define('GDO_SESS_JS', false);
define('GDO_SESS_HTTPS', false);
define('GDO_SESS_LOCK', false);
define('GDO_SESS_SAMESITE', 'lax');

############
### Mail ###
############
define('GDO_ENABLE_EMAIL', false);
define('GDO_BOT_NAME', 'GDOv7 Support Robot');
define('GDO_BOT_EMAIL', 'support@localhost');
define('GDO_ADMIN_EMAIL', 'administrator@localhost');
define('GDO_ERROR_EMAIL', 'errors@localhost');
define('GDO_DEBUG_EMAIL', true);
