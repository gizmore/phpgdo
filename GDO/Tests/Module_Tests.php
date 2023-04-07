<?php
declare(strict_types=1);
namespace GDO\Tests;

use GDO\Core\GDO_Error;
use GDO\Core\GDO_Module;
use GDO\Core\Logger;
use GDO\Util\FileUtil;
use PHPUnit\TextUI\Application;

/**
 * Module that generates Tests from Methods automatically.
 * A good start to easily try many code paths.
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class Module_Tests extends GDO_Module
{

	public int $priority = 1000; # very last


	public function isInstallable(): bool { return false; }


	/**
	 * Run a php unit test suite on a module's /Test/ folder.
	 */
	public static function runTestSuite(GDO_Module $module): void
	{
		global $argv, $argc;

		$skip = [];
		$name = $module->getName();
		if (!\gdo_test::instance()->utility)
		{
			$skip[] = 'CLI';
			$skip[] = 'Crypto';
			$skip[] = 'Date';
			$skip[] = 'Net';
			$skip[] = 'Table';
			$skip[] = 'UI';
			$skip[] = 'User';
		}
		if (in_array($name, $skip, true))
		{
			return;
		}

		$testDir = $module->filePath('Test/');
		if (FileUtil::isDir($testDir))
		{
			echo "---------------------------------------\n";
			echo "---------------------------------------\n";
			echo "Running tests for {$name}!\n";
			flush();
			$argv = [
				'--bootstrap=vendor/autoload.php',
				'--no-progress',
//				'--no-result',
				'--do-not-cache-result',
				'--no-configuration',
				$testDir,
			];
			$argc = count($argv);
			$app = new Application();
			$app->run($argv);
		}
	}


	public function onInstall(): void
	{
		try
		{
			FileUtil::createDir($this->tempPath());
		}
		catch (GDO_Error $ex)
		{
			Logger::logException($ex);
		}
	}

}
