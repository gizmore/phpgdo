<?php
use GDO\Core\Application;
use GDO\DB\Database;
use GDO\Core\Logger;
use GDO\Cronjob\Cronjob;
use GDO\Language\Trans;
use GDO\Core\Debug;
use GDO\CLI\CLI;
use GDO\Core\Method\Stub;

############
### Init ###
############
if (php_sapi_name() !== 'cli')
{
    echo "This is a CLI application.";
    die(-1);
}

require 'GDO7.php';
require 'protected/config.php';

final class gdo_cronjob extends Application
{
	public function isCLI() : bool { return true; }
	public function isCronjob() : bool { return true; }
	
}

global $me;
$me = Stub::make();

gdo_cronjob::instance();
CLI::setServerVars();
Debug::init(GDO_ERROR_DIE, GDO_ERROR_MAIL);
Logger::init('system');
Database::init();
Trans::setISO('en');

/** @var $argv string[] **/
$force = in_array('--force', $argv, true);
Cronjob::run($me && $force);
