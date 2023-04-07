<?php
declare(strict_types=1);

use GDO\CLI\CLI;
use GDO\CLI\REPL;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\Core\ModuleProviders;
use GDO\Date\Time;
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Language\Trans;
use GDO\Perf\GDT_PerfBar;
use GDO\Session\GDO_Session;
use GDO\Tests\Module_Tests;
use GDO\Tests\TestCase;
use GDO\UI\TextStyle;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli')
{
	echo "Tests can only be run from the command line.\n";
	die(-1);
}

system('clear');

echo "######################################\n";
echo "### Welcome to the GDOv7 Testsuite ###\n";
echo "###       Enjoy your flight!       ###\n";
echo "######################################\n";

# Rename the config in case an accident happened.
if ((is_file('protected/config_test2.php')) && (!is_file('protected/config_test.php')))
{
	rename('protected/config_test2.php', 'protected/config_test.php');
}

# Bootstrap GDOv7 with PHPUnit support.
require 'protected/config_test.php';
require 'vendor/autoload.php';
require 'GDO7.php';
Debug::init();
Logger::init('gdo_test');

/**
 * Override a few toggles for unit test mode.
 */
final class gdo_test extends Application
{
	### $options = getopt('acimnqr', ['all', 'config', 'icons', 'methods', 'nulls', 'quick', 'rendering'], $index);


	public bool $all = false;

	public function all(bool $all=true): static
	{
		$this->all = $all;
		if ($all)
		{
			$this->config = true;
			$this->icons = true;
			$this->methods = true;
			$this->nulls = true;
			$this->rendering = true;
		}
		return $this;
	}

	public bool $config = false;

	public function config(bool $config = true): static
	{
		$this->config = $config;
		return $this;
	}


	public bool $icons = false;

	public function icons(bool $icons = true): static
	{
		$this->icons = $icons;
		return $this;
	}


	public bool $methods = false;

	public function methods(bool $methods = true): static
	{
		$this->methods = $methods;
		return $this;
	}

	public bool $nulls = false;

	public function nulls(bool $nulls = true): static
	{
		$this->nulls = $nulls;
		return $this;
	}

	public bool $perf = false;

	public function perf(bool $perf = true): static
	{
		$this->perf = $perf;
		return $this;
	}

	public bool $quick = false;

	public function quick(bool $quick = true): static
	{
		$this->quick = $quick;
		return $this;
	}

	public bool $rendering = false;

	public function rendering(bool $rendering = true): static
	{
		$this->rendering = $rendering;
		return $this;
	}

	public bool $seo = false;

	public function seo(bool $seo = true): static
	{
		$this->seo = $seo;
		return $this;
	}

	public function isUnitTests(): bool
	{
		return true;
	}

	public function isInstall(): bool
	{
		return $this->install;
	}


	public function showHelp(): int
	{
		echo "HELP!\n";
		return 0;
	}

}

$app = gdo_test::init()->modeDetected(GDT::RENDER_CLI)->cli();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
$db = Database::init();

$index = 0;
$options = getopt('acimnpqrs', ['all', 'config', 'icons', 'methods', 'nulls', 'perf', 'quick', 'rendering', 'seo'], $index);

if (isset($options['a']) || isset($options['all']))
{
	$app->all();
}
if (isset($options['c']) || isset($options['config']))
{
	$app->config();
}

if (isset($options['i']) || isset($options['icons']))
{
	$app->icons();
}

if (isset($options['m']) || isset($options['methods']))
{
	$app->methods();
}

if (isset($options['n']) || isset($options['nulls']))
{
	$app->nulls();
}

if (isset($options['p']) || isset($options['perf']))
{
	$app->perf();
}

if (isset($options['q']) || isset($options['quick']))
{
	$app->quick();
}

if (isset($options['r']) || isset($options['rendering']))
{
	$app->rendering();
}

if (isset($options['s']) || isset($options['seo']))
{
	$app->seo();
}

/** @var array $argv * */
$argv = array_slice($argv, $index);
$argc = count($argv);

switch (count($argv))
{
	case 1:
		break;
	default:
		return $app->showHelp();
}


# Confirm
echo 'I will erase the database ' . TextStyle::bold(GDO_DB_NAME) . ".\n";
if (!REPL::confirm('Is this correct?', true))
{
	echo "Abort!\n";
	die(0);
}
define('GDO_TIME_START', microtime(true));

# ##########################
# Simulate HTTP env a bit #
CLI::setServerVars();
# ###########################

/** @var int $argc */
/** @var string[] $argv */

echo 'Dropping Test Database: ' . GDO_DB_NAME . ".\n";
echo "If this hangs, something is locking the db.\n";
$db->dropDatabase(GDO_DB_NAME);
FileUtil::removeDir(GDO_PATH . 'files_test/');
@mkdir(GDO_PATH . 'files_test/', GDO_CHMOD);
FileUtil::removeDir(GDO_TEMP_PATH);
$db->createDatabase(GDO_DB_NAME);
$db->useDatabase(GDO_DB_NAME);

# 1. Try the install process if mode all.
//if ($app->all)
//{
//	echo "NOTICE: Running install all first... for a basic include check.\n";
//	$install = $loader->loadModuleFS('Install', true, true);
//	$install->onLoadLanguage();
//	$loader->initModules();
//	Trans::inited();
//	Module_Tests::runTestSuite($install);
//}

$app->install = false;

if (Application::isError())
{
	CLI::flushTopResponse();
	die(1);
}

# #############
# ## Single ###
# #############
if ($argc === 1) # Specifiy with module names, separated by comma.
{

	global $moduleMappings;
	$moduleMappings = [];

	Filewalker::traverse(GDO_PATH . 'GDO/', null, null, function($entry, $fullpath) {
		global $moduleMappings;
		if (FileUtil::isFile("{$fullpath}/Module_{$entry}.php")) {
			$moduleMappings[strtolower($entry)] = $entry;
		}
	});

	$count = 0;
	$modules = explode(',', $argv[0]);

	foreach ($modules as $k => $modname)
	{
		$beg = $modname[0] === '*' ? 100 : 0;
		$id2 = $modname[0] === '*' ? 1 : 0;
		$id3 = $modname[-1] === '*' ? 1 : 0;
		$str = substr($modname, $id2, -$id3);
		foreach ($moduleMappings as $modname2)
		{
			if (false === ($idx = stripos($modname2, $str)))
			{
				continue;
			}
			if ((!$id2) && ($idx !== 0))
			{
				continue;
			}
			if ((!$id3) && (!str_ends_with($modname2, $str)))
			{
				continue;
			}
		}
		$modules[$k] = $moduleMappings[$modname];
	}

	$modules = array_merge(ModuleProviders::getCoreModuleNames(), $modules);

	# Add Tests, Perf and CLI as dependencies on unit tests.
	if ($app->all || $app->perf)
	{
		$modules[] = 'Perf';
	}

	# Tests Module as a finisher
	$modules[] = 'Tests';

	if ($app->all)
	{
		$modules[] = 'CLI';
		$modules[] = 'Admin';
	}

	# Fix lowercase names
	$modules = array_map(
		function (string $moduleName)
		{
			$module = ModuleLoader::instance()->loadModuleFS($moduleName);
			return $module->getModuleName();
		}, $modules);

	$modules = array_unique($modules);

	while ($count != count($modules))
	{
		$count = count($modules);

		foreach ($modules as $moduleName)
		{
			$module = $loader->loadModuleFS($moduleName, true, true);
			$more = Installer::getDependencyModules($moduleName);
			$more = array_map(function ($m)
			{
				return $m->getModuleName();
			}, $more);
			$modules = array_merge($modules, $more);
			$modules[] = $module->getModuleName();
		}

//		$loader->initModules();

		$modules = array_unique($modules);
	}

	# While loading...

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
elseif ($app->all)
{
	echo "Loading and install all modules from filesystem again...\n";
	$modules = $loader->loadModules();
}
else
{
	return $app->showHelp();
}

if ($app->quick)
{
	$modules = array_filter($modules, function (GDO_Module $module)
	{
		return !in_array($module->getName(), [
			'CountryCoordinates',
			'IP2Country',
			'Tests',
		]);
	});
}

# ######################
# ## Install and run ###
# ######################
if (Installer::installModules($modules))
{
	\GDO\Core\Method\ClearCache::make()->execute();
	$loader->initModules();
	Trans::inited(false);
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
