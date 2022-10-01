<?php
namespace bin;
use GDO\CLI\CLI;
use GDO\Core\Debug;
use GDO\Core\GDO_NoSuchMethodError;
use GDO\Core\GDT_Expression;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Language\Trans;
use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\User\GDO_User;
use GDO\Util\Arrays;
use GDO\UI\GDT_Error;
/**
 * @var $argv string[]
 */
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
global $me;
gdo::init()->cli()->modeDetected(GDT::RENDER_CLI);
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
Database::init();
Cache::init();
Trans::$ISO = GDO_LANGUAGE;
Logger::init(null, GDO_ERROR_LEVEL); # init without username
Debug::init(GDO_ERROR_DIE, GDO_ERROR_MAIL);
$loader->loadModulesCache();
$loader->loadLangFiles();
$loader->initModules();
Trans::inited();
if (!CLI::isCLI())
{
	echo "This GDOv7 binary does only run in the commandline!\n";
	die(-1);
}
CLI::setupUser();
Logger::init(GDO_User::current()->renderUserName(), GDO_ERROR_LEVEL);
define('GDO_CORE_STABLE', 1);

# Shell
if (CLI::isInteractive() && $me)
{
	$line = trim(CLI::getSingleCommandLine());
	if ($line !== '')
	{
		try
		{
			$expression = GDT_Expression::fromLine($line);
			/** @var $result \GDO\Core\GDT_Response **/
			$result = $expression->execute();
			echo $result->render();
		}
		catch (GDO_NoSuchMethodError $ex)
		{
			$module = $ex->module;
			$methods = $module->getMethods();
			$methods = array_map(function(Method $m) {
				return $m->gdoHumanName();
			}, $methods);
			$methods = Arrays::implodeHuman($methods);
			echo t('msg_module_methods', [$module->gdoHumanName(), $methods]);
		}
		catch (\GDO\Core\GDO_Error $ex)
		{
			echo $ex->renderCLI();
		}
		catch (\Throwable $ex)
		{
			Debug::debugException($ex, false);
			echo GDT_Error::fromException($ex)->render();
// 			die(-1);
		}
		finally
		{
			CLI::flushTopResponse();
		}
	}
	else
	{
		echo "Usage: gdo <gdo_expression>. Example: `gdo module.method param,param2`";
		die(0);
	}
}
// else
// {
// 	while ($line = fgets(STDIN))
// 	{
// 		$exp = GDT_Expression::fromLine($line);
// 		echo $line;
// 	}
// }
