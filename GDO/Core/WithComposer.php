<?php
namespace GDO\Core;

/**
 * Add composer support to a module.
 *  
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.2
 */
trait WithComposer
{
	public function includeVendor()
	{
		$path = $this->filePath('vendor/autoload.php');
		require_once $path;
	}
	
	/**
	 * Mark /vendor/ as 3rd party folder.
	 * @return string[]
	 */
	public function thirdPartyFolders() : array
	{
		return ['/vendor/'];
	}
	
}
