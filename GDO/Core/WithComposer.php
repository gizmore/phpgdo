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
	public function includeVendor() : void
	{
		$path = $this->filePath('vendor/autoload.php');
		require_once $path;
	}
	
}
