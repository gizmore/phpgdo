<?php
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Core\Logger;
use PHPUnit\TextUI\Command;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\File\FileUtil;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\DB\Cache;
use GDO\Core\ModuleLoader;
use GDO\CLI\CLI;

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli') { die('Tests can only be run from the command line.'); }

echo "######################################\n";
echo "### Welcome to the GDOv7 testsuite ###\n";
echo "######################################\n";

require 'protected/config_test.php';
require 'GDO7.php';
require 'vendor/autoload.php';

/**
 * Override a few toggles for unit test mode.
 * @author gizmore
 */
final class gdo_test extends Application
{
	private $cli = true;
	public function cli(bool $cli) : self { $this->cli = $cli; return $this; }
	public function isCLI() : bool { return $this->cli; } # override CLI mode to test HTML rendering.
	public function isUnitTests() : bool { return true; }
}

Logger::init('gdo_test', GDO_ERROR_LEVEL);
Debug::init();

$app = new gdo_test();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');

Debug::setMailOnError(GDO_ERROR_EMAIL);
Debug::setDieOnError(false);
Debug::enableErrorHandler();
Debug::enableExceptionHandler();
Cache::init();
Cache::flush();
Database::init(null);
GDO_Session::init(GDO_SESS_NAME, GDO_SESS_DOMAIN, GDO_SESS_TIME, !GDO_SESS_JS, GDO_SESS_HTTPS);

###########################
# Simulate HTTP env a bit  #
CLI::setServerVars();       #
############################

/** @var $argc int **/
/** @var $argv string[] **/

echo "Dropping Test Database: ".GDO_DB_NAME.".\n";
echo "If this hangs, something is locking the db.\n";
Database::instance()->queryWrite("DROP DATABASE IF EXISTS " . GDO_DB_NAME);
Database::instance()->queryWrite("CREATE DATABASE " . GDO_DB_NAME);
Database::instance()->useDatabase(GDO_DB_NAME);

FileUtil::removeDir(GDO_PATH . '/temp_test');

# 1. Try the install process
echo "Running installer first...\n";
$install = $loader->loadModuleFS('Install', true, true);
runTestSuite($install);

echo "Loading modules from filesystem...\n";
$modules = $loader->loadModules(false, true);

if ($argc === 2)
{
    $count = 0;
    $modules = explode(',', $argv[1]);
    
    if ($app->loader->loadModuleFS('Tests', false))
    {
        $modules[] = 'Tests';
    }
    else
    {
        echo "You don't have module gdo6-tests installed, which is probably required to create test users.\n";
        flush();
    }
    
    while ($count != count($modules))
    {
        $count = count($modules);
        
        foreach ($modules as $moduleName)
        {
            $module = ModuleLoader::instance()->getModule($moduleName);
            $more = Installer::getDependencyModules($moduleName);
            $more = array_map(function($m){
                return $m->getName();
            }, $more);
            $modules = array_merge($modules, $more);
            $modules[] = $module->getName();
        }
        $modules = array_unique($modules);
    }
    $modules = array_map(function($m){
        return ModuleLoader::instance()->getModule($m);
    }, $modules);
        
    usort($modules, function(GDO_Module $m1, GDO_Module $m2) {
        return $m1->priority - $m2->priority;
    });

    foreach ($modules as $module)
    {
        echo "Installing {$module->getName()}\n";
        Installer::installModule($module);
        runTestSuite($module);
    }
    echo "Finished.\n";
    return;
}

foreach ($modules as $module)
{
    if (!$module->isPersisted())
    {
        echo "Installing {$module->getName()}\n";
        Installer::installModule($module);
    }
	runTestSuite($module);
}

echo "Finished.\n";

function runTestSuite(GDO_Module $module)
{
    $testDir = $module->filePath('Test');
    if (FileUtil::isDir($testDir))
    {
        echo "Verarbeite Tests für {$module->getName()}\n";
        $command = new Command();
        $command->run(['phpunit', $testDir], false);
        echo "Done with {$module->getName()}\n";
        echo "----------------------------------------\n";
    }
}
