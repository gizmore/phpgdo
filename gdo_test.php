<?php
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Core\Logger;
use GDO\Core\Application;
use GDO\Util\FileUtil;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\CLI\CLI;
use GDO\Tests\Module_Tests;
use GDO\Date\Time;
use GDO\Tests\TestCase;
use GDO\Language\Trans;
use GDO\Perf\GDT_PerfBar;
use GDO\Session\GDO_Session;
use GDO\UI\TextStyle;

define('GDO_TIME_START', microtime(true));

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli')
{
	echo "Tests can only be run from the command line.\n";
	die( -1);
}

echo "######################################\n";
echo "### Welcome to the GDOv7 Testsuite ###\n";
echo "###       Enjoy your flight!       ###\n";
echo "######################################\n";

# Rename the config in case an accident happened.
if ((is_file('protected/config_test2.php')) && ( !is_file('protected/config_test.php')))
{
	rename('protected/config_test2.php', 'protected/config_test.php');
}

# Bootstrap GDOv7 with PHPUnit support.
require 'protected/config_test.php';
require 'GDO7.php';
require 'vendor/autoload.php';
Debug::init();
Logger::init('gdo_test');

/**
 * Override a few toggles for unit test mode.
 */
final class gdo_test extends Application
{

	public function isUnitTests(): bool
	{
		return true;
	}

	public bool $install = true;

	public function isInstall(): bool
	{
		return $this->install;
	}

}
$app = gdo_test::init()->mode(GDT::RENDER_CLI, true)->cli();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
Database::init(null);

# Confirm
echo "I will erase the database " . TextStyle::bold(GDO_DB_NAME) . ".\n";
echo "Is this correct? (Y/n)";
flush();
$c = fread(STDIN, 1);

if (($c !== 'y') && ($c !== 'Y') && ($c !== "\x0D") && ($c !== "\x0A"))
{
	echo "Abort!\n";
	die(0);
}

# ##########################
# Simulate HTTP env a bit #
CLI::setServerVars();
# ###########################

/** @var $argc int **/
/** @var $argv string[] **/

echo "Dropping Test Database: " . GDO_DB_NAME . ".\n";
echo "If this hangs, something is locking the db.\n";
Database::instance()->queryWrite("DROP DATABASE IF EXISTS " . GDO_DB_NAME);
Database::instance()->queryWrite("CREATE DATABASE " . GDO_DB_NAME);
Database::instance()->useDatabase(GDO_DB_NAME);

FileUtil::removeDir(GDO_PATH . 'files_test/');
FileUtil::removeDir(GDO_TEMP_PATH);

# 1. Try the install process if mode all.
if ($argc === 1)
{
	echo "NOTICE: Running install all first... for a basic include check.\n";
	$install = $loader->loadModuleFS('Install', true, true);
	$loader->initModules();
	$install->onLoadLanguage();
	Module_Tests::runTestSuite($install);
}

$app->install = false;

if (Application::$INSTANCE->isError())
{
	CLI::flushTopResponse();
	die(1);
}

# #############
# ## Single ###
# #############
if ($argc === 2) # Specifiy with module names, separated by comma.
{
	$count = 0;
	$modules = explode(',', $argv[1]);

	# Add Tests, Perf and CLI as dependencies on unit tests.
	$modules[] = 'CLI';
	$modules[] = 'Perf';
	$modules[] = 'Tests';

	# Fix lowercase names
	$modules = array_map(
		function (string $moduleName)
		{
			$module = ModuleLoader::instance()->loadModuleFS($moduleName);
			return $module->getName();
		}, $modules);

	# While loading...
	while ($count != count($modules))
	{
		$count = count($modules);

		foreach ($modules as $moduleName)
		{
			$module = $loader->loadModuleFS($moduleName, true, true);
			$more = Installer::getDependencyModules($moduleName);
			$more = array_map(function ($m)
			{
				return $m->getName();
			}, $more);
			$modules = array_merge($modules, $more);
			$modules[] = $module->getName();
		}

		$modules = array_unique($modules);
	}

	# Map
	$modules = array_map(function ($m)
	{
		return ModuleLoader::instance()->getModule($m);
	}, $modules);

	# Sort
	usort($modules, function (GDO_Module $m1, GDO_Module $m2)
	{
		return $m1->priority - $m2->priority;
	});

	# Inited!
}

# ##################
# ## All modules ###
# ##################
else
{
	echo "Loading and install all modules from filesystem again...\n";
	$modules = $loader->loadModules(false, true, true);
}

// $loader = ModuleLoader::instance();
// $loader->initModules();
// Trans::inited(true);

# ######################
# ## Install and run ###
# ######################
// $loader->initModules();
if (Installer::installModules($modules))
{
	Trans::inited(true);
	if (module_enabled('Session'))
	{
		GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS, GDO_SESS_SAMESITE);
		GDO_Session::instance();
	}
	define('GDO_CORE_STABLE', true); # all fine? @deprecated
	foreach ($modules as $module)
	{
		Module_Tests::runTestSuite($module);
	}
}

CLI::flushTopResponse();

# ###########
# ## PERF ###
# ###########
$time = microtime(true) - GDO_TIME_START;
$perf = GDT_PerfBar::make('performance');
$perf = $perf->renderMode(GDT::RENDER_CLI);
printf("Finished with %s asserts after %s.\n%s", TestCase::$ASSERT_COUNT, Time::humanDuration($time), $perf);
