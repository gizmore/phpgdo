<?php
namespace GDO\Tests;

use GDO\Core\GDO_Module;
use GDO\Util\FileUtil;
use PHPUnit\TextUI\Command;

/**
 * Module that generates Tests from Methods automatically.
 * A good start to easily try many code paths.
 *
 * @version 7.0.1
 * @since 6.10.0
 * @author gizmore
 */
final class Module_Tests extends GDO_Module
{

	public int $priority = 1000; # very last

	/**
	 * Run a php unit test suite on a module's /Test/ folder.
	 */
	public static function runTestSuite(GDO_Module $module): void
	{
		$testDir = $module->filePath('Test/');
		if (FileUtil::isDir($testDir))
		{
			echo "Running tests for {$module->getName()}\n";
			$command = new Command();
			$exit = false;
			$command->run(['phpunit', $testDir], $exit);
			echo "Done with {$module->getName()}\n";
			echo "----------------------------------------\n";
		}
	}

	public function isInstallable(): bool { return false; }

	public function onInstall(): void
	{
		FileUtil::createDir($this->tempPath());
	}

}
