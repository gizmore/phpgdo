<?php
namespace GDO\Core;

/**
 * Add composer support to a module.
 *  
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithComposer
{
	private bool $composerIncluded = false;
	
	public function includeVendor() : void
	{
		if (!$this->composerIncluded)
		{
			$path = $this->filePath('vendor/autoload.php');
			require $path;
			$this->composerIncluded = true;
		}
	}
	
}
