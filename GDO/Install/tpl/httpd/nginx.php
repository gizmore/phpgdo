<?php
namespace GDO\Install\tpl\httpd;

$webRoot = '/' . trim(GDO_WEB_ROOT, '/') . '/';
if ($webRoot === '//')
{
    $webRoot = '/';
}

$prefix = $webRoot === '/' ? '' : rtrim($webRoot, '/');
$indexURI = $prefix . '/index.php';
$location = $prefix . '/';
$routeRegex = $prefix ? ('^' . preg_quote($prefix, '#') . '/?(.*)$') : '^/(.*)$';

# Make nginx's document root line up with GDO_WEB_ROOT.
$documentRoot = rtrim(GDO_PATH, '/\\');
$levels = $prefix ? count(array_filter(explode('/', trim($prefix, '/')))) : 0;
for ($i = 0; $i < $levels; $i++)
{
    $documentRoot = dirname($documentRoot);
}

$site = preg_replace('/[^a-zA-Z0-9_.-]/', '_', GDO_SITENAME);
$phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
?>
#
# nginx config suggestion for PHPGDO
# Generated via: ./gdo_adm.sh nginx
#
server {
    listen 80;
    listen [::]:80;
    server_name <?=GDO_DOMAIN?>;

    root <?=$documentRoot?>;
    index index.php;
    charset utf-8;
    client_max_body_size 16m;

    access_log /var/log/nginx/<?=$site?>.access.log;
    error_log  /var/log/nginx/<?=$site?>.error.log;

    # ACME/Let's Encrypt challenge files.
    location ^~ /.well-known/acme-challenge/ {
        root /var/www/acme;
        try_files $uri =404;
    }

<?php if ($prefix): ?>
    location = <?=$prefix?> {
        return 301 <?=$webRoot?>;
    }

<?php endif; ?>
    # The site root is handled by PHPGDO's front controller.
    location = <?=$webRoot?> {
        rewrite ^ <?=$indexURI?>?_url= last;
    }

    # Never expose runtime data or installer internals.
    location ^~ <?=$prefix?>/protected/ { deny all; }
    location ^~ <?=$prefix?>/temp/      { deny all; }
    location ^~ <?=$prefix?>/files/     { deny all; }
    location ^~ <?=$prefix?>/install/   { deny all; }

    # Execute only the public front controller.
    location = <?=$indexURI?> {
        include /etc/nginx/fastcgi_params;
        fastcgi_param SCRIPT_FILENAME <?=rtrim(GDO_PATH, '/\\')?>/index.php;
        fastcgi_param SCRIPT_NAME <?=$indexURI?>;
        fastcgi_param DOCUMENT_ROOT <?=$documentRoot?>;
        fastcgi_param HTTP_PROXY "";

        # Debian/Ubuntu default. Adjust when your PHP-FPM socket differs.
        fastcgi_pass unix:/run/php/php<?=$phpVersion?>-fpm.sock;
        # Alternative TCP setup:
        # fastcgi_pass 127.0.0.1:9000;
    }

    # No other PHP source file may be executed or downloaded.
    location ~ \.php(?:/|$) {
        return 404;
    }

    # Serve existing public assets directly; route everything else to PHPGDO.
    location <?=$location?> {
        try_files $uri @phpgdo;
    }

    location @phpgdo {
        rewrite <?=$routeRegex?> <?=$indexURI?>?_url=$1 last;
    }

    # Block dotfiles except /.well-known/, handled above.
    location ~ (^|/)\.(?!well-known(?:/|$)) {
        deny all;
    }
}
