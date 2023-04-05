<?php
namespace bin;

use GDO\CLI\CLI;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Error;
use GDO\Core\GDO_NoSuchMethodError;
use GDO\Core\GDT;
use GDO\Core\GDT_Expression;
use GDO\Core\GDT_Response;
use GDO\Core\Logger;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Language\Trans;
use GDO\UI\GDT_Error;
use GDO\User\GDO_User;
use GDO\Util\Arrays;
use Throwable;

/**
 * @var string[] $argv
 */
# The GDOv7 CLI bootstrap.
require __DIR__ . '/../protected/config.php';
if (!defined('GDO_CONFIGURED'))
{
	echo "GDOv7 is not installed here.\n";
	die(Application::EXIT_ERROR);
}
require __DIR__ . '/../GDO7.php';

$gdo = new class extends Application
{

	public function __construct()
	{
		parent::__construct();
		self::$INSTANCE = $this;
	}

	public function isCLI(): bool { return true; }

};

global $me;
$gdo->cli()->verb(GDT_Form::GET)->modeDetected(GDT::RENDER_CLI);
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
Database::init();
CLI::init();
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
if (CLI::isInteractive())
{
	$line = trim(CLI::getSingleCommandLine());
	$_SERVER['REQUEST_URI'] = $line;
	if ($line !== '')
	{
		try
		{
			$expression = GDT_Expression::fromLine($line);
			/** @var GDT_Response $result * */
			$result = $expression->execute();
			CLI::flushTopResponse();
			echo $result->render();
		}
		catch (GDO_NoSuchMethodError $ex)
		{
			$module = $ex->module;
			$methods = $module->getMethods();
			$methods = array_map(function (Method $m)
			{
				return $m->gdoShortName();
			}, $methods);
			$methods = Arrays::implodeHuman($methods);
			echo t('msg_module_methods', [
				$module->gdoShortName(), $methods]);
		}
		catch (GDO_Error $ex)
		{
			CLI::flushTopResponse();
			echo $ex->renderCLI();
		}
		catch (Throwable $ex)
		{
			Debug::debugException($ex, false);
			CLI::flushTopResponse();
			echo GDT_Error::fromException($ex)->render();
		}
	}
	else
	{
		echo "Usage: gdo <gdo_expression>. Example: `gdo module.method param,param2`\n";
		die(0);
	}
}
 else
 {
 	while ($line = fgets(STDIN))
 	{
 		$exp = GDT_Expression::fromLine($line);
 		echo $line;
 	}
 }
