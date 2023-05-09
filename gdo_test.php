<?php
declare(strict_types=1);

use GDO\CLI\CLI;
use GDO\CLI\REPL;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\Logger;
use GDO\Core\Method\ClearCache;
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
use GDO\User\GDO_User;
use GDO\Util\FileUtil;
use GDO\Util\Filewalker;
use function PHPUnit\Framework\assertTrue;

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli')
{
	echo "Test can only be run from the command line.\n";
	die(-1);
}

/** @var int $argc */
/** @var string[] $argv */
system('clear');
define('GDO_TIME_START', microtime(true));

echo "######################################\n";
echo "### Welcome to the GDOv7 Testsuite ###\n";
echo "###       Enjoy your flight!       ###\n";
echo "######################################\n";

# Rename the config in case an accident happened.
if ((is_file('protected/config_test2.php')) &&
	(!is_file('protected/config_test.php')))
{
	rename('protected/config_test2.php', 'protected/config_test.php');
}

# Bootstrap GDOv7 with PHPUnit support.
require 'protected/config_test.php';
require 'vendor/autoload.php';
require 'GDO7.php';
CLI::init();
Debug::init();
Logger::init('gdo_test');
Logger::disableBuffer();

/**
 * Override a few toggles for unit test mode.
 */
final class gdo_test extends Application
{

	public bool $all = false;

	public function all(bool $all=true): static
	{
		$this->all = $all;
		if ($all)
		{
			$this->blanks = true;
			$this->config = true;
			$this->double = true;
			$this->friends = true;
			$this->icons = true;
			$this->methods = true;
			$this->nulls = true;
			$this->parents = true;
			$this->rendering = true;
			$this->seo = true;
			$this->utility = true;
		}
		return $this;
	}

	public bool $blanks = false;

	public function blanks(bool $blanks = true): static
	{
		$this->blanks = $blanks;
		$this->verboseMessage("The auto test for blanks got enabled!");
		return $this;
	}


	public bool $config = false;

	public function config(bool $config = true): static
	{
		$this->config = $config;
		$this->verboseMessage("The auto test for configurations got enabled!");
		return $this;
	}


	public bool $double = false;

	public function double(bool $double = true): static
	{
		$this->double = $double;
		$this->verboseMessage("The auto test for double install got enabled!");
		return $this;
	}


	public bool $friends = false;

	public function friends(bool $friends = true): static
	{
		$this->friends = $friends;
		$this->verboseMessage("The auto test for friendencies got enabled!");
		return $this;
	}


	public bool $icons = false;

	public function icons(bool $icons = true): static
	{
		$this->icons = $icons;
		$this->verboseMessage("The auto test for icons got enabled!");
		return $this;
	}

	public bool $live = false;

	public function live(bool $live = true): static
	{
		$this->live = $live;
		$this->verboseMessage("The live mode got enabled!");
		return $this;
	}


	public bool $methods = false;

	public function methods(bool $methods = true): static
	{
		$this->methods = $methods;
		$this->verboseMessage("The auto test for methods got enabled!");
		return $this;
	}

	public bool $nulls = false;

	public function nulls(bool $nulls = true): static
	{
		$this->nulls = $nulls;
		$this->verboseMessage("The auto test for empties got enabled!");
		return $this;
	}

	public bool $parents = false;

	public function parents(bool $parents = true): static
	{
		$this->parents = $parents;
		$this->verboseMessage("The auto test for dependencies got enabled!");
		return $this;
	}

	public array $childs;
	public function addParents(array $parents): static
	{
		$this->childs = $parents;
		return $this;
	}

	public function isParentWanted(string $moduleName, bool $allowCore=false): bool
	{
		if ($allowCore && ModuleLoader::instance()->getModule($moduleName)->isCoreModule())
		{
			return true;
		}
		return ($this->parents) || ($moduleName === 'Tests') ||
			in_array($moduleName, $this->childs, true);
	}

	public bool $quick = false;

	public function quick(bool $quick = true): static
	{
		$this->quick = $quick;
		$this->verboseMessage("The quick mode got enabled!");
		return $this;
	}

	public bool $rendering = false;

	public function rendering(bool $rendering = true): static
	{
		$this->rendering = $rendering;
		$this->verboseMessage("The auto test for rendering got enabled!");
		return $this;
	}

	public bool $seo = false;

	public function seo(bool $seo = true): static
	{
		$this->seo = $seo;
		$this->verboseMessage("The auto test for SEO got enabled!");
		return $this;
	}

	public bool $utility = false;

	public function utility(bool $utility = true): static
	{
		$this->utility = $utility;
		$this->verboseMessage("The test for utilites got enabled!");
		return $this;
	}

	public bool $verbose = false;
	public function verbose(bool $verbose=true): static
	{
		$this->verbose = $verbose;
		return $this;
	}

	public function isUnitTestVerbose(): bool
	{
		return $this->verbose;
	}

	public function isUnitTests(): bool
	{
		return true;
	}

	public bool $install = true;
	public function isInstall(): bool
	{
		return $this->install;
	}


	public function showHelp(int $code=0): int
	{
		global $argv;
		$app = "php {$argv[0]}";
		echo "{$app} 0 <module1,module2,... Use % as wildcard>\n";
		echo "\n";
		echo "Use $app '%' to run tests on all modules\n";
		echo "\n";
		echo "--all = Run all test options.\n";
		echo "--blanks = Run blank GDO creation tests.\n";
		echo "--config = Run all configuration and settings test options.\n";
		echo "--double = Run the install process two times.\n";
		echo "--friends = Treat all friendencies as dependency.\n";
		echo "--icons = Run icon tests (DELETE?).\n";
		echo "--live = Use the normal config.php. Do not erase the DB.\n";
		echo "--methods = Run execution tests.\n";
		echo "--nulls = Run all empty creation tests (DELETE?).\n";
		echo "--parents = Run automated tests on all dependencies.\n";
		echo "--quick = Skip slow big data tests.\n";
		echo "--rendering = Run rendering tests.\n";
		echo "--seo = Run i18n tests.\n";
		echo "--utility = Run utility tests.\n";
		echo "--verbose = Print verbose message information.\n";
		return $code;
	}

	public function verboseMessage(string $string): void
	{
		if ($this->isUnitTestVerbose())
		{
			printf("%s: %s\n", TextStyle::italic('Note'), $string);
			if (ob_get_level())
			{
				ob_flush();
			}
		}
	}
}

$app = gdo_test::init()->modeDetected(GDT::RENDER_CLI)->cli();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
$db = Database::init();

$index = 0;
$options = getopt('abcdfhimnpqrsuv', ['all', 'blanks', 'config', 'double', 'friends', 'help', 'icons', 'methods', 'nulls', 'perf', 'quick', 'rendering', 'seo', 'utility', 'verbose'], $index);
$opcount = 0;

if (isset($options['v']) || isset($options['verbose']))
{
	$app->verbose();
	echo "Verbosity got enabled!\n";
}

if (isset($options['a']) || isset($options['all']))
{
	$app->all();
	$opcount++;
}

if (isset($options['b']) || isset($options['blanks']))
{
	$app->blanks();
	$opcount++;
}

if (isset($options['c']) || isset($options['config']))
{
	$app->config();
	$opcount++;
}

if (isset($options['d']) || isset($options['double']))
{
	$app->double();
	$opcount++;
}

if (isset($options['f']) || isset($options['friends']))
{
	$app->friends();
	$opcount++;
}

if (isset($options['h']) || isset($options['help']))
{
	return $app->showHelp();
}

if (isset($options['i']) || isset($options['icons']))
{
	$app->icons();
	$opcount++;
}

if (isset($options['m']) || isset($options['methods']))
{
	$app->methods();
	$opcount++;
}

if (isset($options['n']) || isset($options['nulls']))
{
	$app->nulls();
	$opcount++;
}


if (isset($options['p']) || isset($options['parents']))
{
	$app->parents();
	$opcount++;
}

if (isset($options['q']) || isset($options['quick']))
{
	$app->quick();
}

if (isset($options['r']) || isset($options['rendering']))
{
	$app->rendering();
	$opcount++;
}

if (isset($options['s']) || isset($options['seo']))
{
	$app->seo();
	$opcount++;
}

if (isset($options['u']) || isset($options['utility']))
{
	$app->utility();
	$opcount++;
}

$argv = array_slice($argv, $index);
$argc = count($argv);

switch ($argc)
{
	case 0:
		$app->all();
		$argv = ['%'];
		$argc = 1;

	case 1:
		break;

	default:
		return $app->showHelp(-1);
}


# Confirm
echo 'I will erase the database ' . TextStyle::bold(GDO_DB_NAME) . ".\n";
if (!REPL::confirm('Is this correct?', true))
{
	echo "Abort!\n";
	die(0);
}

echo 'Dropping Test Database: ' . GDO_DB_NAME . ".\n";
echo "If this hangs, something is locking the db.\n";
$db->dropDatabase(GDO_DB_NAME);
FileUtil::removedDir(GDO_PATH . 'files_test/');
@mkdir(GDO_PATH . 'files_test/', GDO_CHMOD);
FileUtil::removedDir(GDO_TEMP_PATH);
$db->createDatabase(GDO_DB_NAME);
$db->useDatabase(GDO_DB_NAME);

if (Application::isError())
{
	$app->verboseMessage("Application error. Halting!");
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
	}, 0);

	$count = 0;
	$modules2 = explode(',', $argv[0]);
	$modules = [];

	foreach ($modules2 as $k => $modname)
	{
		$modname = trim(strtolower($modname));
		$beg = $modname[0] === '%' ? 100 : 0;
		$id2 = $modname[0] === '%' ? 1 : 0;
		$id3 = $modname[-1] === '%' ? 1 : 0;
		$str = trim($modname, "% \t\n\r\0\x0B");
		if ($str)
		{
			foreach ($moduleMappings as $modname2 => $modname)
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
				$modules[] = $modname;
			}
		}
		else
		{
			$modules = array_values($moduleMappings);
		}
	}

	if (!count($modules))
	{
		throw new \GDO\Core\GDO_Exception('No module found for ' . html($argv[0]));
	}
	else
	{
		$msg = sprintf("%d Modules match the pattern %s.\n", count($modules), TextStyle::bold(html($argv[0])));
		$app->verboseMessage($msg);
	}

	$modules = array_unique($modules);
	$app->addParents($modules);

	$modules = array_merge(ModuleProviders::getCoreModuleNames(), $modules);
	$modules = array_unique($modules);

	# Test Module as a finisher
	if ($opcount > 0)
	{
		$modules[] = 'Tests';
	}

	if ($app->utility)
	{
		$modules[] = 'Admin';
		$modules[] = 'CLI';
		$modules[] = 'Crypto';
		$modules[] = 'Net';
	}

	if ($app->config)
	{
		$modules[] = 'Admin';
	}

	$modules = array_unique($modules);

	while ($count != count($modules))
	{
		$count = count($modules);
		foreach ($modules as $moduleName)
		{
			$module = $loader->loadModuleFS($moduleName);
			$more = Installer::getDependencyNames($moduleName);
			$more = $app->friends ? array_merge($more, Installer::getFriendencyNames($moduleName)) : $more;
			$modules = array_merge($modules, $more);
			$modules[] = $module->getModuleName();
		}
		$modules = array_unique($modules);
	}

	# Map
	$modules = array_map(function (string $m)
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
else
{
	return $app->showHelp(-1);
}

# Filter
$skip = [];

if ($app->quick)
{
	$skip[] = 'CountryCoordinates';
	$skip[] = 'Geo2City';
	$skip[] = 'IP2Country';
	$app->verboseMessage('Skipping a few modules in quick mode: ' . implode(', ', $skip));
}
$modules = array_filter($modules, function (GDO_Module $module) use ($skip)
{
	return !in_array($module->getName(), $skip, true);
});

# ######################
# ## Install and run ###
# ######################
Trans::inited(); // Need langfiles for installer...
if (Installer::installModules($modules))
{
	$loader->initModules(); # now we can init really...
	if ($app->double)
	{
		$app->verboseMessage('Installing all selected modules again, for double installer check.');
		assertTrue(
			Installer::installModules($modules, true),
			'Test if double install works with forced migration.');
	}
	$app->verboseMessage('Leaving installer mode.');
	$app->install = false;
	ClearCache::make()->execute();
	$loader->initModules();
	if (module_enabled('Session'))
	{
		$app->verboseMessage('Activating CLI Session handling.');
		GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS, GDO_SESS_SAMESITE);
		GDO_Session::instance();
	}
	define('GDO_CORE_STABLE', true); # all fine? @deprecated
	foreach ($modules as $module)
	{
		Module_Tests::runTestSuite($module);
	}
}

//CLI::flushTopResponse();

# ###########
# ## PERF ###
# ###########
$time = microtime(true) - GDO_TIME_START;
$perf = GDT_PerfBar::make('performance');
$perf = $perf->renderMode(GDT::RENDER_CLI);
printf("Finished with %s asserts after %s.\n%s", TestCase::$ASSERT_COUNT, Time::humanDuration($time), $perf);
