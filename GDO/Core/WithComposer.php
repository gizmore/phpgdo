<?php
namespace GDO\Core;

/**
 * Add composer support to a module.
 *  
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.0
 */
trait WithComposer
{
	
	public function includeVendor(): void
	{
		static $composerIncluded = false;
		if (!$composerIncluded)
		{
			$composerIncluded = true;
			require $this->filePath('vendor/autoload.php');
		}
	}
	
}
