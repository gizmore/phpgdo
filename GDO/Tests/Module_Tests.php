<?php
namespace GDO\Tests;

use GDO\Core\GDO_Module;
use GDO\File\FileUtil;
use PHPUnit\TextUI\Command;

/**
 * Module that generates Tests from Methods automatically.
 * A good start to try many code paths.
 * 
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class Module_Tests extends GDO_Module
{
    public int $priority = 5;
    
    public function onInstall() : void
    {
        FileUtil::createDir($this->tempPath());
    }
    
    /**
     * Run a php unit test suite on a module /Test/ folder.
     * @param GDO_Module $module
     */
    public static function runTestSuite(GDO_Module $module) : void
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

}
