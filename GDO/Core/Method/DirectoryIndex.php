<?php
namespace GDO\Core\Method;

use GDO\Core\GDT;
use GDO\File\GDT_Path;
use GDO\Table\MethodTable;
use GDO\File\GDO_File;

/**
 * Simple GET method that lists the contents of a folder.
 * Uses Filecache.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.11.3
 */
final class DirectoryIndex extends MethodTable
{
	public function gdoTable()
	{
		return GDO_File::table();
	}
	
	public function gdoParameters() : array
	{
		return [
			GDT_Path::make('_url')->existingDir(),
		];
	}
	
	public function execute(): GDT
	{
		$path = $this->gdoParameterVar();
		var_dump($path);
	}


	
}
