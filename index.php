<?php
use GDO\Core\Logger;
use GDO\Core\Debug;
use GDO\DB\Database;

@include 'protected/config.php';

if (!defined('GDO_CONFIGURED'))
{
	require 'index_install.php';
}

Logger::init(null, GDO_ERROR_LEVEL);
Debug::init();
Database::init();

