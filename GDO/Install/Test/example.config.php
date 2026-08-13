<?php
# This file only exists to resolve warnings in my IDE.

####################
### GDOv7 Config ###
####################
# if (defined('GDO_CONFIGURED')) return; # double include (needed?)
error_reporting(E_ALL & ~E_DEPRECATED); # Should be not less than E_All & ~E_DEPRECATED & ~E_STRICT.
ini_set('display_errors', 'On'); # Should be enabled / does not matter because of \GDO\Core\Debug.

/**
 * Please work down each section carefully.
 *
 * Common pitfalls:
 *
 * - The config rewrites itself upon gdo_update.sh!
 * -
 * - There are 2 domain settings: GDO_DOMAIN and GDO_SESS_DOMAIN.
 * - GDO_DB_ENABLED is easily overlooked.
 *
 * (c)2021-2023 - gizmore@wechall.net
 * re-created by GDOv7.0.3-r1830 on ---.
 **/
define('GDO_CONFIGURED', '1');

############
### Site ###
############
define('GDO_SITENAME', 'GDOv7'); # should be configured via sitename language key.
define('GDO_HOSTNAME', 'mogwai@home'); # Server hostname. Used in error emails so you can distingush different servers.
define('GDO_ENV', 'dev'); # Environment can be dev, tes or pro.
define('GDO_SEO_URLS', true); # Enable SEO style URLs. Requires url rewriting for your httpd.
define('GDO_SITECREATED', '2026-07-27 20:57:04.359'); # Automatically generated on config generation.
define('GDO_LANGUAGE', 'en'); # Default Language setting. Should be 'en'
define('GDO_TIMEZONE', 'UTC'); # Server Timezone for logfiles.
define('GDO_THEMES', 'lup,bs5,default'); # Comma separated themechain list. Tried from left to right. Example: 'tbs,classic,default'.
define('GDO_MODULE', 'LinkUUp'); # Default module for startpage.
define('GDO_IPC', 'db'); # IPC mode can be: db, ipc or none.
define('GDO_IPC_DEBUG', false); # IPC event logging.
define('GDO_GDT_DEBUG', 0); # GDT debugging level. 0: off, 1: counters, 2: instancelog.
define('GDO_JSON_DEBUG', true); # global JSON_PRETTY toggle.

############
### HTTP ###
############
define('GDO_DOMAIN', 'lup.localhost'); # Website domain. Should match cookie domain.
define('GDO_SERVER', 'apache2.4'); # webserver software; apache2.2, apache2.4, nginx, none.
define('GDO_WEB_ROOT', '/phpgdo/'); # Website root folder. Usually "/" or "/phpgdo/".
define('GDO_PORT', 80); # Default port for generating links.
define('GDO_PROTOCOL', 'http'); # Website preferred protocol. Either http or https.
define('GDO_FORCE_SSL', false); # Allow only HTTPS?

#############
### Files ###
#############
define('GDO_FILES_DIR', 'files'); # Filepath for physical files. Change this in config_test.php
define('GDO_CHMOD', 0700); # File creation chmod value. Ignore on windows.
define('GDO_PREPROCESSOR', false); # File preprocessor to speed up dev code.

###############
### Logging ###
###############
define('GDO_LOG_REQUEST', true); # Log every request?
define('GDO_LOG_PROFILE', ''); # Generate URLs with xdebug profiler trigger?
define('GDO_ERROR_LEVEL', 0x37ff); # Log level
define('GDO_ERROR_STACKTRACE', true); # Show stacktrace to users?
define('GDO_ERROR_DIE', true); # Die on every little warning and notice?
define('GDO_SEND_ERROR_MAILS', true); # Send PHP/GDO error report mails?

################
### Database ###
################
define('GDO_SALT', 'jShP1OLfgiZpE0uh'); # Cryptograpycally secure salt to strengthen tokens and passwords.
define('GDO_DB_ENABLED', true); # DB enabled? (required atm)
define('GDO_DB_READONLY', false); # DB in read only mode? (except installers)
define('GDO_DB_HOST', 'localhost'); # DB hostname.
define('GDO_DB_PORT', 3306); # DB port.
define('GDO_DB_USER', 'phpgdo'); # DB username
define('GDO_DB_PASS', 'phpgdo'); # DB password
define('GDO_DB_NAME', 'phpgdo'); # DB database name or SQLite filename
define('GDO_DB_ENGINE', 'MyIsam'); # DB engine: InnoDB,MyIsam(MySQL), JournalMode(SQLite).
define('GDO_DB_DEBUG', 0); # GDO debugging level. 0: off, 1: counters, 2: instancelog.

#############
### Cache ###
#############
define('GDO_CACHE_DEBUG', 0); # Cache debugging level. 0: off, 1: setters, 2: setter-with-backtraces.
define('GDO_FILECACHE', false); # Enable phpgdo filecache?
define('GDO_MEMCACHE', 0); # Enable memcached? 0: off, 1: on, 2: fallback via filecache.
define('GDO_MEMCACHE_HOST', '127.0.0.1'); # memcached host.
define('GDO_MEMCACHE_PORT', 61221); # memcached port.
define('GDO_MEMCACHE_TTL', 1800); # memcached time to live.

###############
### Cookies ###
###############
define('GDO_SESS_NAME', 'GDO7'); # Cookie name
define('GDO_SESS_DOMAIN', 'lup.localhost'); # Cookie domain. Use .domain.com for all subdomains.
define('GDO_SESS_TIME', 604800); # Session lifetime in seconds
define('GDO_SESS_JS', false); # Session cookie only secure via JS?
define('GDO_SESS_HTTPS', false); # Session only for https?
define('GDO_SESS_LOCK', false); # Lock sessions during request?
define('GDO_SESS_SAMESITE', 'lax'); # Session samesite settings. lax: recommended. none: wont work. strict: needs setup.

############
### Mail ###
############
define('GDO_ENABLE_EMAIL', false); # Enable E-Mail sending?
define('GDO_BOT_NAME', 'GDOv7 Support Robot'); # Robot Mail sender Name
define('GDO_BOT_EMAIL', 'support@localhost'); # Robot Mail sender Mail
define('GDO_ADMIN_EMAIL', 'administrator@localhost'); # Administrator Mail
define('GDO_ERROR_MAIL_RECIPIENTS', 'errors@localhost'); # Recipients of PHP/GDO errors and Core 403/404 notification mails; separate by comma.
define('GDO_DEBUG_EMAIL', true); # Enable Print to Screen debugging?

#################
### SMTP Mail ###
#################
define('GDO_SMTP_HOST', 'mogwai'); # SMTP host
define('GDO_SMTP_PORT', 587); # SMTP port
define('GDO_SMTP_USER', ''); # SMTP username
define('GDO_SMTP_PASS', ''); # SMTP password
