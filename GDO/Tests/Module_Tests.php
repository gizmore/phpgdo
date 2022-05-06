<?php
namespace GDO\Tests;

use GDO\Core\GDO_Module;
use GDO\File\FileUtil;

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
    public int $priority = 4;
    
    public function onInstall() : void
    {
        FileUtil::createDir($this->tempPath());
    }
    
}
