<?php

use GDO\Install\Config;

?>
<h2><?=t('install_title_1')?></h2>

<p><?=t('install_text_1', [Config::linkStep(2)]);?></p>

<p><?=t('install_text_2');?></p>

<pre>
CREATE DATABASE gdo7;
GRANT ALL ON gdo7.* TO gdo7@localhost IDENTIFIED BY 'gdo7';
</pre>
