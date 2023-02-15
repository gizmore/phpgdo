<?php
use GDO\Admin\Method\Install;
use GDO\CLI\CLI;
use GDO\CLI\REPL;
use GDO\Core\Application;
use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\GDO_ModuleVar;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Method;
use GDO\Core\Logger;
use GDO\Core\ModuleLoader;
use GDO\Core\ModuleProviders;
use GDO\Core\Website;
use GDO\Crypto\BCrypt;
use GDO\DB\Cache;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Install\Config;
use GDO\Install\Installer;
use GDO\Install\Module_Install;
use GDO\Install\Method\Configure;
use GDO\Install\Method\InstallCronjob;
use GDO\Install\Method\Security;
use GDO\Install\Method\SystemTest;
use GDO\Install\Method\Webserver;
use GDO\Language\Trans;
use GDO\UI\GDT_Error;
use GDO\UI\GDT_Success;
use GDO\User\GDO_Permission;
use GDO\User\GDO_User;
use GDO\User\GDO_UserPermission;
use GDO\User\GDT_UserType;
use GDO\Util\FileUtil;
use GDO\Util\PP;
use GDO\Util\Strings;
use GDO\Core\GDT;
use GDO\Install\GDT_DocsFile;

/**
 * The gdo_adm.php CLI application manages administrative tasks.
 * It shall not be accesible to normal users!
 *
 * @example ./gdo_adm.sh systemtest
 * @example ./gdo_adm.sh configure
 * @example ./gdo_adm.sh provide Helpdesk
 * @example ./gdo_adm.sh install_all
 * @example ./gdo_adm.sh secure
 * @example ./gdo_adm.sh configure perf enabled 1
 *         
 * @author gizmore
 * @version 7.0.2
 * @since 6.10.0
 *       
 * @see gdo_adm.sh
 * @see gdo_update.sh - to update your gdo6 installation
 * @see gdo_reset.sh - to reset your installation to factory defaults. Own files are not deleted
 * @see gdo_test.sh - for unit testing
 * @see gdo_yarn.sh - to install javascript dependencies
 * @see gdo_post_install.sh - to finish the installation process
 * @see gdo_cronjob.sh - to run the module cronjobs
 * @see bin/gdo - to invoke a gdo6 method via the CLI - for normal usage.
 */

/** @var $argc int **/
/** @var $argv string[] **/

/**
 * Show usage of the gdoadm.sh shell command.
 *
 * @example gdoadm.sh install MailGPG
 * @example bin/gdo mail.send giz,Hi there,, you wanker,This is the mail body. Parameters are separated via comma.
 */
function printUsage(int $code = -1): void
{
	global $argv;
	$exe = $argv[0];
	echo "Usage:\n";
	echo "\n--- Spawn ---\n";
	echo "php $exe docs - Print install instructions from the DOCS folder https://github.com/gizmore/phpgdo/tree/main/DOCS\n";
	echo "php $exe systemtest - To run the installer system test.\n";
	echo "php $exe configure [<config.php>] - To generate a protected/config.php.\n";
	echo "php $exe test [<config.php>] - To test your protected/config.php\n";
	echo "php $exe admin <username> <password> - to (re)set an admin account\n";
	echo "php $exe cronjob - To print cronjob instructions\n";
	echo "php $exe apache - To print apache-2.4 configuration suggestions\n";
	echo "php $exe nginx(todo) - To print nginx configuration suggestions\n";
	echo "php $exe secure - To secure your installation after install.\n";
	echo "\n--- Modules ---\n";
	echo "php $exe modules [<module>] - To show a list of modules or show module details\n";
	echo "php $exe provide <module> - To download all modules that are required to provide a module\n";
	echo "php $exe provide_all - To download and install all available modules. This for devs who work on global module stuff and unit testing\n";
	echo "php $exe install <module> - To install a module and it's dependencies\n";
	echo "php $exe install_all - To install all modules inside the GDO/ folder and their dependencies\n";
	echo "php $exe wipe <module> - To uninstall modules\n";
	echo "php $exe wipe_all - To erase the whole database\n";
	echo "\n--- Updates ---\n";
	echo "php $exe update - Is automatically called after gdo_update.sh - it re-installs all installed modules.\n";
	echo "php $exe confgrade - Upgrade your config.php with new config vars.\n";
	echo "php $exe migrate <module> - To force-migrate gdo tables for an installed module. Handle with care.\n";
	echo "php $exe migrate_all - To force-migrate all gdo tables for all installed modules. Handle with special care.\n";
	echo "php $exe pp - To run the PP php-preprocessor on all files.\n";
	echo "\n--- Module config ---\n";
	echo "php $exe config <module> - To show the config variables for a module\n";
	echo "php $exe config <module> <key> - To show the description for a module variable\n";
	echo "php $exe config <module> <key> <var> - To change the value of a module variable\n";
	echo PHP_EOL;
	echo "Tip: you can have a 'cli-only' protected/config_cli.php\n";
	die($code);
}

chdir(__DIR__);

if ($argc === 1)
{
	printUsage(0);
}

require 'GDO7.php';

# forced?
if (FileUtil::isFile('protected/config_cli.php'))
{
	require 'protected/config_cli.php';
}
else # try :]
{
	@include 'protected/config.php';
}

final class gdo_adm extends Application
{

	/**
	 * Parse options and purge them from argv.
	 */
	public function parseOptions(): void
	{
		global $argv, $argc;
		
		$i = 0;
		$o = getopt('qs', ['quiet', 'ssh'], $i);

		if (isset($o['q'])||isset($o['quiet']))
		{
			$this->quiet();
		}
		
		if (isset($o['s'])||isset($o['ssh']))
		{
			$this->ssh();
		}
		
		# Fix argc/argv
		$cmd = $argv[0];
		$argv = array_slice($argv, $i + 1);
		array_unshift($argv, $cmd);
		$argc = count($argv);
	}
	
	public function isInstall(): bool
	{
		return true;
	}
	
	###############
	### Options ###
	###############
	public bool $quiet = false;
	public function quiet(bool $quiet=true): self
	{
		$this->quiet = $quiet;
		return $this;
	}
	
	public bool $ssh = false;
	public function ssh(bool $ssh=true): self
	{
		$this->ssh = $ssh;
		return $this;
	}
	
}

$app = gdo_adm::init();
$app->modeDetected(GDT::RENDER_CLI)->cli()->verb(GDT_Form::POST);

# Load config defaults
if ( !defined('GDO_CONFIGURED'))
{
	define('GDO_DB_ENABLED', false);
	define('GDO_WEB_ROOT', '/');
	\GDO\Install\Config::configure(); # autoconfig
}

// new ModuleLoader(GDO_PATH . 'GDO/');
CLI::setServerVars();
Database::init();
Cache::flush();
Cache::fileFlush();
Trans::$ISO = GDO_LANGUAGE;
Logger::init('gdo_adm', GDO_ERROR_LEVEL); # init without username
Debug::init(false, false);
$loader = ModuleLoader::instance();

define('GDO_CORE_STABLE', true);

$app->parseOptions();
$command = $argv[0];
$db = !!GDO_DB_ENABLED;
switch ($command)
{
	case 'configure':
	case 'systemtest':
	case 'wipe_all':
		$db = false;
		break;
}

$loader->loadModules($db, true);
$loader->loadLangFiles(true);
$loader->initModules();

# Run!

if ($command === 'docs')
{
	echo "Welcome to the GDOv7 interactive installer.\n";
	$gdt = GDT_DocsFile::make('file');
	$now = "first";
	while (REPL::confirm("Do you want, here, to print a few lines of the phpgdo DOCS of https://github.com/gizmore/phpgdo?", false))
	{
		while (!$gdt->hasError())
		{
			$gdt->reset(true);
			if (REPL::changedGDTVar($gdt, "Which File $now? "))
			{
				echo file_get_contents($gdt->getDocsPath());
				echo "\n";
				$now = "now";
			}
			else
			{
				break 2;
			}
		}
	}
	REPL::confirmOrDie("The biggest requirements are mysql/mariadb, git[4window] and PHP>=8.0, OK?\n", false);
	if (REPL::confirm("You can check the requirements with `./gdo_adm.sh systemtest`.", false))
	{
		system("php gdo_adm.php systemtest");
		REPL::confirmOrDie("Was the system test OK?\n");
	}
	echo "You need a file named `protected/config.php`.\n";
	if (REPL::confirm("Shall i run `php gdo_adm.php configure` for you?", true))
	{
		system("php gdo_adm.php configure -i");
	}
	REPL::abortable("Next we will run `php gdo_adm.php test`, Ok? ");
	if ($code = system("php gdo_adm.php test"))
	{
		die("`php gdo_adm.php test` failed with exit code $code");
	}
	echo "Good, you can connect to your database.\n";
	if (REPL::confirm("Do you want to install the core now? ", true))
	{
		echo "Ok, running `php gdo_adm.php install core` in a second...\n";
		sleep(1);
		echo "Hehe\n";
		if ($code = system("php gdo_adm.php install core --post-install"))
		{
			die("`php gdo_adm.php install core` failed with exit code $code");
		}
	}
	
	$folder = GDO_PATH . 'bin/';
	echo "You now can add $folder to your PATH environment variable.\n";
	if (REPL::acknowledge("Do you want to see instructions?"))
	{
		echo "TODO\n";
	}

	
	echo "See you around!\n";
	die(0);
}

elseif ($command === 'systemtest')
{
	if ($argc !== 2)
	{
		printUsage(1);
	}
	echo SystemTest::make()->execute()->renderCLI();
	echo PHP_EOL;
}

elseif ($command === 'configure')
{
	if ($argc === 2)
	{
		$argc++;
		$argv[2] = 'config.php'; # default config filename
	}
	
	$inputs = [
		'filename' => $argv[2],
		'save_config' => '1',
	];
	
	if (REPL::confirm(t('repl_adm_interactive'), false))
	{
		$n = 0;
		$fields = Config::fields();
		$count = count($fields);
		while ($n < $count)
		{
			$gdt = $fields[$n];
			$gdt->reset(true);
			if (!$gdt->isWriteable())
			{
				$n++;
			}
			else
			{
				REPL::changeGDT($gdt);
				if (!$gdt->hasError())
				{
					$n++;
				}
			}
		}
	}
	
	$response = Configure::make()->executeWithInputs($inputs, false);
	echo $response->render();
	if (Application::isSuccess())
	{
		Security::make()->protectFolders();
		echo "You should now edit this file by hand.\n";
		echo "Afterwards execute {$argv[0]} test config.\n";
	}
}

elseif ($command === 'test')
{
	if (GDO_DB_ENABLED)
	{
		Database::init();
	}
	echo \GDO\Install\Method\SystemTest::make()->execute()->renderCLI();

	echo "---\n\n\n";
	echo "Your configuration seems solid.\n";
	echo "You can now try to php {$argv[0]} install <module>.\n";
	echo "A list of official modules is shown via php {$argv[0]} modules.\n";
	echo "Before you can install a module, you have to clone it.\n";
	echo "Example: cd GDO; git clone --recursive https://github.com/gizmore/gdo6-session-cookie Session; cd ..\n";
	echo "Please note that some modules have multiple providesrs.";
	echo "E.g, for the session module, you can choose between phpgdo-session-db and phpgdo-session-cookie.\n";
	echo "Any Session provider has just to be cloned as a folder named GDO/Session/, voila.\n";
}

elseif ($command === 'modules')
{
	if ($argc == 2)
	{
		echo "List of official modules\n";
		$providers = ModuleProviders::$PROVIDERS;
		$git = \GDO\Core\ModuleProviders::GIT_PROVIDER;
		foreach ($providers as $moduleName => $p)
		{
			if ( !is_array($p))
				$p = [
					$p
				];
			foreach ($p as $provider)
			{
				printf("%32s: cd GDO; git clone --recursive {$git}{$provider} {$moduleName}; cd ..\n", $moduleName);
			}
		}
	}
	elseif ($argc == 3)
	{
		$moduleName = $argv[2];
		$module = ModuleLoader::instance()->getModule($moduleName, true);
		if ( !$module)
		{
			echo "Module not found.\n";

			$providers = @ModuleProviders::$PROVIDERS[$moduleName];
			if ( !$providers)
			{
				echo "{$moduleName}: Not an official module or a typo somewhere. No Provider known.\n";
				echo "Try reading docs/INSTALL.MD\n";
				echo "You can get a list of modules via {$argv[0]}.\n";
			}
			elseif (is_array($providers))
			{
				echo "{$moduleName}: Choose between multiple possible providers:\n";
				foreach ($providers as $provider)
				{
					printf("%20s: cd GDO; git clone --recursive {$git}{$provider} {$moduleName}; cd ..\n", $moduleName);
				}
			}
			else
			{
				printf("%20s: cd GDO; git clone --recursive {$git}{$providers} {$moduleName}; cd ..\n", $moduleName);
			}
		}
		else
		{
			$deps = implode(', ', $module->getDependencies());
			echo "Module: {$moduleName}\n";
			echo "License: {$module->license}\n";
			echo $module->getModuleDescription();
			echo "\n";
			if ($deps)
			{
				echo "Dependencies: {$deps}\n";
			}
		}
	}
	else
	{
		printUsage();
	}
}

elseif (($command === 'install') || ($command === 'install_all'))
{
	if ( !GDO_DB_ENABLED)
	{
		echo "You do not have GDO_DB_ENABLED. I cannot install anything.\n";
		die(1);
	}

	$deps = [];

	if ($command === 'install')
	{
		$mode = 1;
		if ($argc !== 3)
		{
			printUsage();
		}
	}
	elseif ($command === 'install_all')
	{
		$mode = 2;
		if ($argc !== 2)
		{
			printUsage();
		}
	}

	ModuleLoader::instance()->reset()->loadModules(true, true, true);
	ModuleLoader::instance()->initModules();
	$git = \GDO\Core\ModuleProviders::GIT_PROVIDER;

	if ($mode === 1)
	{
		$deps[] = 'Core';
		$moduleNames = explode(',', $argv[2]);
		foreach ($moduleNames as $moduleName)
		{
			$module = ModuleLoader::instance()->loadModuleFS($moduleName);
			$deps[] = $module->getName();
			if ( !$module)
			{
				echo "Unknown module. Try {$argv[0]} modules.\n";
				die(1);
			}
			$deps = array_merge($deps, $module->getDependencies());
		}
		$deps = array_unique($deps);
	}
	elseif ($mode === 2)
	{
		$modules = ModuleLoader::instance()->loadModules(true, true, true);
		$modules = array_filter($modules, function (GDO_Module $module)
		{
			return $module->isInstallable();
		});
		$deps = array_map(function (GDO_Module $mod)
		{
			return $mod->getName();
		}, $modules);

		$cnt = count($deps);
		echo "Installing all {$cnt} modules.\n";
	}

	$deps[] = 'Core';
	$deps = array_unique($deps);

	$cnt = 0;
	$allResolved = true; # All required modules provided?
	while ($cnt !== count($deps))
	{
		$cnt = count($deps);
		foreach ($deps as $dep)
		{
			$depmod = ModuleLoader::instance()->getModule($dep, true);

			if ( !$depmod)
			{
				if ($allResolved === true)
				{
					echo "Missing dependencie(s)!\n";
					echo "Please note that this list may not be complete, because missing modules might have more dependencies.\n";
				}
				$allResolved = false;
				$providers = @\GDO\Core\ModuleProviders::$PROVIDERS[$dep];
				if ( !$providers)
				{
					echo "{$dep}: Not an official module or a typo somewhere. No Provider known.\n";
				}
				elseif (is_array($providers))
				{
					echo "{$dep}: Choose between multiple possible providers.\n";
					foreach ($providers as $provider)
					{
						printf("%20s: cd GDO; git clone --recursive {$git}{$provider} {$dep}; cd ..\n", $dep);
					}
				}
				else
				{
					printf("%20s: cd GDO; git clone --recursive {$git}{$providers} {$dep}; cd ..\n", $dep);
				}

				continue;
			}

			$deps = array_unique(array_merge($depmod->getDependencies(), $deps));
		}
	}

	$deps = array_unique($deps);

	if ( !$allResolved)
	{
		echo "Some required modules are not provided by your current GDO/ folder.\n";
		echo "Please clone the modules like stated above.\n";
		echo "Repeat the process until all dependencies are resolved.\n";
		die(2);
	}

	$deps2 = [];
	foreach ($deps as $moduleName)
	{
		$mod = ModuleLoader::instance()->getModule($moduleName);
		$deps2[$moduleName] = $mod->priority;
	}
	sort($deps);
	asort($deps2);
	echo t('msg_installing_modules', [
		implode(', ', $deps)
	]) . "\n";

	$modules = array_map(function (string $moduleName)
	{
		return ModuleLoader::instance()->getModule($moduleName);
	}, array_keys($deps2));
	Installer::installModules($modules);
	
// 	if (GDO_PREPROCESSOR)
// 	{
// 		echo "Running php-preprocessor on all modules.\n";
// 		PP::init()->processFolder(GDO_PATH);
// 	}
	
	Cache::flush();
	Cache::fileFlush();
	echo "Done.\n";
}

elseif ($command === 'admin')
{
	if ($argc !== 4)
	{
		printUsage();
	}
	if ( !($user = GDO_User::getByName($argv[2])))
	{
		$user = GDO_User::blank([
			'user_name' => $argv[2],
			'user_type' => GDT_UserType::MEMBER,
		])->insert();
		GDT_Hook::callWithIPC('UserActivated', $user, null);
	}
	if (module_enabled('Login'))
	{
		$user->saveSettingVar('Login', 'password', BCrypt::create($argv[3])->__toString());
	}
	$user->saveVar('user_deleted', null);
	$user->saveVar('user_deletor', null);
	foreach (GDO_Permission::table()->all() as $perm)
	{
		GDO_UserPermission::grant($user, $perm->getName());
	}
	$user->changedPermissions();
	echo t('msg_admin_created', [
		$argv[2]
	]) . "\n";
}

elseif ($command === 'wipe_all')
{
	if ($argc !== 2)
	{
		printUsage();
	}
	Database::instance()->queryWrite("DROP DATABASE " . GDO_DB_NAME);
	Database::instance()->queryWrite("CREATE DATABASE " . GDO_DB_NAME);
	printf("The database has been killed completely and created empty.\n");
}

elseif ($command === 'wipe')
{
	if ($argc !== 3)
	{
		printUsage();
	}

	$module = ModuleLoader::instance()->loadModuleFS($argv[2]);

	$response = Install::make()->executeWithInputs([
		'module' => $module->getName(),
		'uninstall' => '1'
	]);

	if (Application::isError())
	{
		echo $response->renderCLI();
	}
	else
	{
		if ($classes = $module->getClasses())
		{
			$classes = array_map(function ($class)
			{
				return Strings::rsubstrFrom($class, '\\');
			}, $classes);
		}
		else
		{
			$classes = [];
		}
		printf("The %s module has been wiped from the database.\n", $module->getName());
		if ($classes)
		{
			printf("The following GDOs have been wiped: %s.\n", implode(', ', $classes));
		}
	}
}

elseif ( ($command === 'config') || ($command === 'conf') )
{
	if (($argc < 2) || ($argc > 5))
	{
		printUsage();
	}
	
	$loader->initModules();

	if ($argc === 2)
	{
		$modules = ModuleLoader::instance()->getEnabledModules();
		$names = array_map(function (GDO_Module $module)
		{
			return $module->getName();
		}, $modules);
		echo t('msg_installed_modules', [
			implode(', ', $names)
		]) . "";
		die(0);
	}
	if ($argc === 3)
	{
		$module = ModuleLoader::instance()->loadModuleFS($argv[2], false, true);
		if (( !$module) || ( !$module->isPersisted()))
		{
			echo t('err_module', [
				html($argv[2])
			]) . "\n";
			die( -1);
		}
		if ($config = $module->getConfigCache())
		{
			$vars = [];
			foreach ($config as $key => $gdt)
			{
				$vars[] = $key;
			}
			$keys = implode(', ', $vars);
			$keys = $keys ? $keys : t('none');
			echo t('msg_available_config', [
				$module->getName(),
				$keys
			]);
			echo PHP_EOL;
			die(0);
		}
		else
		{
			echo t('msg_module_has_no_config');
			die(1);
		}
	}

	$key = $argv[3];
	if ($argc === 4)
	{
		$module = ModuleLoader::instance()->loadModuleFS($argv[2], false, true);
		if (( !$module) || ( !$module->isPersisted()))
		{
			echo t('err_module_disabled', [
				html($argv[2])
			]) . "\n";
			die( -1);
		}
		$config = $module->getConfigColumn($key);
		echo t('msg_set_config', [
			$key,
			$module->getName(),
			$config->initial,
			$config->gdoExampleVars()
		]);
		echo PHP_EOL;
		die(0);
	}

	$var = $argv[4];
	if ($argc === 5)
	{
		$module = ModuleLoader::instance()->loadModuleFS($argv[2], false, true);
		if (( !$module) || ( !$module->isPersisted()))
		{
			echo t('err_module_disabled', [
				html($argv[2])
			]) . "\n";
			die( -1);
		}
		$gdt = $module->getConfigColumn($key)->var($var);
		if ( !$gdt->validate($gdt->toValue($var)))
		{
			echo json_encode($gdt->configJSON());
			echo PHP_EOL;
			die(1);
		}
		GDO_ModuleVar::createModuleVar($module, $gdt);
		echo t('msg_changed_config',
			[
				$gdt->renderLabel(),
				$module->getName(),
				$gdt->displayVar($gdt->initial),
				$gdt->displayVar($gdt->var),
			]);
		echo PHP_EOL;
		Cache::flush();
		GDT_Hook::callHook('ModuleVarsChanged', $module);
	}
}

elseif (($command === 'provide') || ($command === 'provide_all'))
{
	if ($argc < 3)
	{
		if (($argc < 2) || ($command !== 'provide_all'))
		{
			printUsage( -1);
		}
	}

	$isall = strpos($command, '_all') !== false;
	$isssh = $app->ssh;

	$loader = ModuleLoader::instance();

	$loader->loadModules(false, true, true);

	# Get all dependencies
	$cd = 0;
	if ($isall)
	{
		$deps = [
			'Core'
		];
		foreach (ModuleProviders::$PROVIDERS as $name => $providers)
		{
			if ($providers)
			{
				$deps[] = $name;
			}
		}
	}
	else # Single
	{
		$deps = ModuleProviders::getDependencies($argv[2]);
		if ($deps === null)
		{
			echo "Unknown module: {$argv[2]}\n";
			die( -1);
		}
		$deps[] = 'Core';
		$deps[] = $argv[2];
		while ($cd != count($deps))
		{
			$cd = count($deps);
			foreach ($deps as $dep)
			{
				if ($module = $loader->getModule($dep, false, false))
				{
					$moreDeps = $module->getDependencies();
				}
				else
				{
					$moreDeps = ModuleProviders::getDependencies($dep);
				}
				$deps = array_unique(array_merge($deps, $moreDeps));
			}
		}
	}

	$deps = array_unique($deps);

	# Sort by name
	sort($deps);

	# Check missing in fs
	$missing = [];
	foreach ($deps as $dep)
	{
		if ( !$loader->getModule($dep, false, false))
		{
			$missing[] = $dep;
		}
	}

	if (count($missing))
	{
		echo "The following modules are not in your filesystem:\n";
		echo implode(', ', $missing) . "\n";
		echo "Shall i git clone those modules? (y/n) [y]: ";
		$input = $app->quiet ? '' : readline();
		$input = trim(strtolower($input));
		if (($input === 'y') || ($input === ''))
		{
			echo "Cloning " . count($missing) . " modules...\n";
			foreach ($missing as $module)
			{
				$providers = ModuleProviders::getProviders($module);

				if ( !$providers)
				{
					echo "No provider for $module\n";
					die(1);
				}
				$n = 1;
				if (is_array($providers))
				{
					echo "The {$module} Module has more than 1 possible provider. Please choose: \n";
					$n = 0;
					foreach ($providers as $provider)
					{
						$n++;
						echo "{$n}: {$provider}\n";
					}
					echo "Please choose: [1-{$n}]: ";
					$n = $app->quiet ? 1 : ((int) readline());
					if (($n < 1) || ($n > count($providers)))
					{
						echo "Invalid choice!\nExit 1\n";
						die(1);
					}
				}
				$url = ModuleProviders::getGitUrl($module, $n, $isssh);
				$module = ModuleProviders::getCleanModuleName($module);
				$cmd = "cd GDO && git clone --recursive {$url} {$module}";
				echo $cmd . "\n";
				$output = [];
				$retval = 0;
				exec($cmd, $output, $retval);
				echo implode("\n", $output) . "\n";
				echo "Return code: " . $retval . "\n";
				if ($retval != 0)
				{
					echo "Error upon clone. Exiting\n";
					die($retval);
				}
			}
			echo "You should now have all dependencies cloned in your GDO/ folder.\n";
			
			if ($isall)
			{
				echo "You can now:\n./gdoadm.sh install_all\n";
			}
			else
			{
				echo "You can now:\n./gdoadm.sh install {$argv[2]}\n";
			}
			$r = $app->quiet ? 'y' : readline("Shall i do this now? [Y/n]");
			$r = $r ? $r : 'y';
			if (($r[0] === 'y') || ($r[0] === 'Y'))
			{
				
				if ($isall)
				{
					system("php gdo_adm.php install_all");
				}
				else 
				{
					system("php gdo_adm.php install {$argv[2]}");
				}
			}
		}
	}
	else
	{
		if ($isall)
		{
			echo "Your filesystem has all the required modules. You can: ./gdoadm.sh install_all\n";
		}
		else
		{
			echo "Your filesystem has all the required modules. You can: ./gdoadm.sh install {$argv[2]}\n";
		}
		$r = $app->quiet ? 'y' : readline("Shall i do this now? [Y/n]");
		$r = $r ? $r : 'y';
		if (($r[0] === 'y') || ($r[0] === 'Y'))
		{
			if ($isall)
			{
				system("php gdo_adm.php install_all");
			}
			else
			{
				system("php gdo_adm.php install {$argv[2]}");
			}

			if (GDO_PREPROCESSOR)
			{
				system("php gdo_adm.php pp");
			}
			
			system("bash gdo_post_install.sh --quiet");
		}
	}
}

elseif ($command === 'cronjob')
{
	$module = Module_Install::instance();
	$method = InstallCronjob::make();
	$result = $method->executeWithInit();
	echo $result->renderCLI();
}

elseif ($command === 'secure')
{
	$module = Module_Install::instance();
	$method = GDT_Method::make()->method(Security::make());
	$result = $method->clibutton()
		->noChecks()
		->execute();
	echo $result->renderCLI();
}

elseif ($command === 'update')
{
	system("php gdo_adm.php --quiet provide_all");
	$loader->loadModules(true, true, true);
	$modules = ModuleLoader::instance()->getModules();
	foreach ($modules as $module)
	{
		Installer::installModule($module);
	}
	if (GDO_PREPROCESSOR)
	{
		system('php gdo_adm.php pp');
	}
	echo "Update finished.\n";
}

elseif ($command === 'pp')
{
	echo "Running php-preprocessor on all modules.\n";
	$pp = PP::init();
	$pp->input(GDO_PATH)->recurse()->replace();
	if ($pp->execute())
	{
		echo "Completed.\n";
	}
}

elseif ($command === 'migrate')
{
	if (count($argv) !== 3)
	{
		Website::error('Admin', 'err_gdoadm_migrate');
	}
	else if ( !($module = ModuleLoader::instance()->getModule($argv[2], true, false)))
	{
		echo GDT_Error::make()->text('err_module', [
			html($argv[2])
		])->renderCLI();
	}
	else
	{
		Installer::installModule($module, true);
		GDT_Success::make()->text('msg_gdoadm_migrated', [
			$module->renderName()
		]);
	}
}

elseif ($command === 'migrate_all')
{
	$modules = ModuleLoader::instance()->getEnabledModules();
	foreach ($modules as $module)
	{
		Installer::installModule($module, true);
	}
	echo GDT_Success::make()->text('msg_gdoadm_migrated_all')->renderCLI();
}

elseif ($command === 'gizmore_setup')
{
	$special = ModuleProviders::getMultiProviders();
	foreach ($special as $moduleName => $providers)
	{
		echo "Cloning especially {$moduleName}.\n";
		foreach ($providers as $i => $provider)
		{
			echo "Cloning provider {$provider}.\n";
			$url = ModuleProviders::getGitUrl($moduleName, $i + 1, true);
			$cmd = "cd GDO && git clone --recursive {$url}";
			$output = [];
			$return_var = 0;
			echo "$cmd\n";
			$result = exec($cmd, $output, $return_var);
			if ($return_var !== 0)
			{
				CLI::error(t('err_clone_url', [
					$url
				]));
			}
		}

		$dir = GDO_PATH . "GDO/{$moduleName}/.git";
		echo "Cleaning provider choice in use: {$dir}.\n";
		FileUtil::removeDir($dir);
	}
	echo "All done, gizmore sire o\"! =)\n";
}

elseif ($command === 'confgrade')
{
	switch ($argc)
	{
		default:
			printUsage();
		case 2:
			$argv[2] = 'config.php';
		case 3:
			$path = $argv[2];
	}
	
	Installer::refreshConfig("protected/{$path}");
	
	echo "Config file `{$path}` has been rewritten.\n";
}

elseif ($command === 'apache')
{
	$docs = Webserver::make()->apache24();
	echo $docs->render();
}

else
{
	echo "Unknown command {$command}\n\n";
	printUsage();
}

CLI::flushTopResponse();
