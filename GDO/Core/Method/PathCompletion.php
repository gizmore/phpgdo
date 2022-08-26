<?php
namespace GDO\Core\Method;

use GDO\Core\MethodCompletion;
use GDO\Core\GDT_JSON;

/**
 * Path completion for GDT_Path.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class PathCompletion extends MethodCompletion
{
	public function execute()
	{
		$query = $this->getSearchTerm();
		$files = glob("{$query}.*");
		return GDT_JSON::make()->value($files);
	}
	
}
