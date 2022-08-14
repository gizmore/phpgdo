<?php
namespace bin;

use GDO\CLI\CLI;
use GDO\Core\Debug;
use GDO\Core\GDT_Expression;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\Application;
use GDO\Core\GDT;

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
gdo::instance()->cli()->mode(GDT::RENDER_CLI, true);
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
Database::init();
Cache::init();
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # init without username
Debug::init(GDO_ERROR_DIE, GDO_ERROR_MAIL);
$loader->loadModulesCache();
define('GDO_CORE_STABLE', 1);
CLI::setupUser();
# Shell
if (!CLI::isCLI())
{
	echo "This GDOv7 binary does only run in the commandline!\n";
}
elseif (CLI::isInteractive())
{
	$line = CLI::getSingleCommandLine();
	$expression = GDT_Expression::fromLine($line);
	$result = $expression->execute();
	echo $result->renderCLI();
// 	echo GDT_Page::$INSTANCE->topResponse()->renderCLI();
}
// else
// {
// 	while ($line = fgets(STDIN))
// 	{
// 		$exp = GDT_Expression::fromLine($line);
// 		echo $line;
// 	}
// }
