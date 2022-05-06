<?php
use GDO\Core\Logger;
use GDO\Core\Debug;

@include 'protected/config.php';

if (!defined('GDO_CONFIGURED'))
{
	require 'index_install.php';
}

Logger::init(null);
Debug::init();

