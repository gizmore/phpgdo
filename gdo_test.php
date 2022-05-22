<?php
use GDO\DB\Database;
use GDO\Install\Installer;
use GDO\Core\Logger;
use GDO\Core\Application;
use GDO\Session\GDO_Session;
use GDO\Util\FileUtil;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\ModuleLoader;
use GDO\CLI\CLI;
use GDO\Tests\Module_Tests;
use GDO\Date\Time;
use GDO\Tests\TestCase;
use GDO\Language\Trans;

define('GDO_TIME_START', microtime(true));

/**
 * Launch all unit tests.
 * Unit tests should reside in <Module>/Test/FooTest.php
 */
if (PHP_SAPI !== 'cli')
{
	echo "Tests can only be run from the command line.\n";
	die(-1);
}

echo "######################################\n";
echo "### Welcome to the GDOv7 Testsuite ###\n";
echo "###       Enjoy your flight!       ###\n";
echo "######################################\n";

# Rename the config in case an accident happened.
if ( (is_file('protected/config_test2.php')) &&
	 (!is_file('protected/config_test.php')) )
{
	rename('protected/config_test2.php', 'protected/config_test.php');
}

# Bootstrap GDOv7 wit PHPUnit support.
require 'protected/config_test.php';
require 'GDO7.php';
require 'vendor/autoload.php';

/**
 * Override a few toggles for unit test mode.
 * @author gizmore
 */
final class gdo_test extends Application
{
	public function isUnitTests() : bool { return true; }
}

Logger::init('gdo_test', GDO_ERROR_LEVEL);
Debug::init();

gdo_test::instance();
$loader = new ModuleLoader(GDO_PATH . 'GDO/');
// Cache::init();
// Cache::flush();
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

FileUtil::removeDir(GDO_PATH . 'files_temp/');
FileUtil::removeDir(GDO_TEMP_PATH);

# 1. Try the install process if mode all.
if ($argc === 1)
{
	echo "NOTICE: Running install all first... for a basic include check.\n";
	$install = $loader->loadModuleFS('Install', true, true);
	Module_Tests::runTestSuite($install);
}

##############
### Single ###
##############
if ($argc === 2) # Specifiy with module names, separated by comma.
{
    $count = 0;
    $modules = explode(',', $argv[1]);
    
    while ($count != count($modules))
    {
        $count = count($modules);
        
        foreach ($modules as $moduleName)
        {
            $module = $loader->loadModuleFS($moduleName, true, true);
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

    Trans::inited(true);
    
    foreach ($modules as $module)
    {
        echo "Installing {$module->getName()}\n";
        Installer::installModule($module);
        Module_Tests::runTestSuite($module);
    }
}
else # All!
{
	echo "Loading all modules from filesystem...\n";
	$modules = $loader->loadModules(false, true, true);
	
	foreach ($modules as $module)
	{
	    if (!$module->isPersisted())
	    {
	        echo "Installing {$module->getName()}\n";
	        Installer::installModule($module);
	    }
	    Module_Tests::runTestSuite($module);
	}
}

############
### PERF ###
############
$time = microtime(true) - GDO_TIME_START;
printf("Finished %s asserts after %s.\n", TestCase::$ASSERT_COUNT, Time::humanDuration($time));
