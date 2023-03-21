<?php
namespace GDO\Install\tpl\httpd;

use GDO\CLI\CLI;

?>
#
# Apache 2.4 config suggestion
#
<VirtualHost *:80>
    ServerName "<?=GDO_DOMAIN?>"
    DocumentRoot "<?=GDO_PATH?>"
    AllowEncodedSlashes NoDecode # SEO
    <Directory
    "<?=GDO_PATH?>">
    Options +FollowSymLinks -MultiViews
    AllowOverride None # speedup
    Require all granted
    # All requests go to gdo
    RewriteEngine on # slowdown
    RewriteCond %{REQUEST_URI} !^<?=GDO_WEB_ROOT?>index.php$
    RewriteCond %{REQUEST_URI} !^<?=GDO_WEB_ROOT?>install/
    # (TBS) RewriteCond %{REQUEST_URI} !^<?=GDO_WEB_ROOT?>GDO/TBS/challenges/
    RewriteCond %{REQUEST_URI} !^<?=GDO_WEB_ROOT?>.well-known/
    RewriteRule ^(.*)$ <?=GDO_WEB_ROOT?>index.php?_url=$1&%1 [QSA,L]
    </Directory>
    <Directory
    "<?=GDO_FILES_DIR?>">
    Options -FollowSymLinks -MultiViews
    AllowOverride None
    Require all denied
    </Directory>
    <Directory
    "<?=GDO_PATH?>protected">
    Options -FollowSymLinks -MultiViews
    AllowOverride None
    Require all denied
    </Directory>
    <Directory
    "<?=GDO_PATH?>temp">
    Options -FollowSymLinks -MultiViews
    AllowOverride None
    Require all denied
    </Directory>
    <Directory
    "<?=GDO_PATH?>install">
    Options -FollowSymLinks -MultiViews
    AllowOverride None
    Require all denied
    </Directory>
    #
    # Suggested:
    # mpm_itk: AssignUserID <?=CLI::getUsername()?> <?=CLI::getUsername()?>
    #
    # LOG
    ErrorLog "<?=GDO_PATH?>protected/logs/<?=GDO_SITENAME?>.error.log"
    CustomLog "<?=GDO_PATH?>protected/logs/<?=GDO_SITENAME?>.access.log" combined
    #
    # # TLS
    # SSLProtocol all -SSLv2
    # SSLCipherSuite HIGH:!aNULL:!MD5
    # SSLCertificateFile /root/.acme.sh/tbs.wechall.net/fullchain.cer
    # SSLCertificateKeyFile /root/.acme.sh/tbs.wechall.net/tbs.wechall.net.key
</VirtualHost>	
