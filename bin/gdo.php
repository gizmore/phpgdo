<?php
namespace bin;

use GDO\CLI\CLIUtil;
use GDO\Core\Debug;
use GDO\Core\GDT_Expression;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\Application;

# The GDOv7 CLI bootstrap.
define('GDO_BIN_PATH', str_replace('\\', '/', __DIR__) . '/');
require GDO_BIN_PATH . '../protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
	echo "GDOv7 is not installed here.\n";
	die(1);
}
require GDO_BIN_PATH . '../GDO7.php';

class gdo extends Application
{
	public function isCLI() : bool { return true; }
	
}
new gdo();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
Database::init();
Cache::init();
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # init without username
Debug::init();
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Debug::setDieOnError(GDO_ERROR_DIE);
Debug::setMailOnError(GDO_ERROR_MAIL);
$loader->loadModules(GDO_DB_ENABLED, !GDO_DB_ENABLED);

define('GDO_CORE_STABLE', 1);


# Shell
if (!CLIUtil::isCLI())
{
	echo "This GDOv7 binary does only run in the commandline!\n";
}
elseif (CLIUtil::isInteractive())
{
	$line = CLIUtil::getSingleCommandLine();
	$expression = GDT_Expression::fromLine($line);
	$result = $expression->execute();
	echo $result->renderCLI();
}
// else
// {
// 	while ($line = fgets(STDIN))
// 	{
// 		$exp = GDT_Expression::fromLine($line);
// 		echo $line;
// 	}
// }
