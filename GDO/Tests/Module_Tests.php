<?php
declare(strict_types=1);
namespace GDO\Tests;

use GDO\Core\GDO_Exception;
use GDO\Core\GDO_Module;
use GDO\Core\Logger;
use GDO\UI\TextStyle;
use GDO\Util\FileUtil;
use PHPUnit\TextUI\Application;

/**
 * Module that generates Test from Methods automatically.
 * A good start to easily try many code paths.
 *
 * @version 7.0.3
 * @since 6.10.0
 * @author gizmore
 */
final class Module_Tests extends GDO_Module
{
	/** @var string[] */
	private static array $testDirs = [];

	public int $priority = 1000; # very last


	public function isInstallable(): bool { return false; }


	/**
	 * Queue a module's PHPUnit suite. PHPUnit 10 seals its global event facade
	 * after a run, so one process can no longer create a fresh Application for
	 * every module as PHPUnit 9 allowed.
	 */
	public static function runTestSuite(GDO_Module $module): void
	{
		global $argv, $argc;

		$app = \gdo_test::instance();
		$skip = [];
		$name = $module->getName();
		if (!$app->utility)
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
			$app->verboseMessage("Skipping Module_{$name} for not checking utitlity tests.");
			return;
		}

		if (!$app->isParentWanted($name, true))
		{
			$app->verboseMessage("Skipping Module_{$name} for not being selected.");
			return;
		}

		$testDir = $module->filePath('Test/');
		if (FileUtil::isDir($testDir))
		{
			self::$testDirs[] = $testDir;
		}
	}

	/**
	 * Run every queued module suite in one PHPUnit process.
	 */
	public static function runQueuedSuites(): void
	{
		if (!self::$testDirs)
		{
			return;
		}

		$names = array_map(static fn(string $dir): string => basename(dirname($dir)), self::$testDirs);
		echo "\n---------------------------------------\n";
		echo 'Running tests for: ' . TextStyle::bold(implode(', ', $names));
		echo "\n---------------------------------------\n";
		flush();
		$argv = [
				'--bootstrap=vendor/autoload.php',
				'--no-progress',
				'--do-not-cache-result',
				'--no-configuration',
			];
		$argv = array_merge($argv, self::$testDirs);
		$app = new Application();
		$app->run($argv);
		self::$testDirs = [];
	}


	public function onInstall(): void
	{
		try
		{
			FileUtil::createDir($this->tempPath());
		}
		catch (GDO_Exception $ex)
		{
			Logger::logException($ex);
		}
	}

}
