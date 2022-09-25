<?php
namespace GDO\Core\Method;

use GDO\Core\GDO;
use GDO\Core\MethodCompletion;
use GDO\Core\GDT_JSON;
use GDO\UI\TextStyle;
use GDO\Core\GDO_FileCache;

/**
 * Path completion for GDT_Path.
 * Can filter for dirs or for files.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class PathCompletion extends MethodCompletion
{
	protected function gdoTable(): GDO
	{
		# Actually fake and not used!
		return GDO_FileCache::table();
	}
	
	public function execute()
	{
		$query = $this->getSearchTerm();
		$files = glob("{$query}*");
		$json = [];
		$max = $this->getMaxSuggestions();
		foreach ($files as $file)
		{
			if (is_readable($file))
			{
				$json[] = [
					'id' => $file,
					'text' => $file,
					'display' => TextStyle::underline($file),
				];
			}
			if (count($json) >= $max)
			{
				break;
			}
		}
		return GDT_JSON::make()->value($json);
	}
	
}
