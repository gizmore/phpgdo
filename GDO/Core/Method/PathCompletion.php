<?php
declare(strict_types=1);
namespace GDO\Core\Method;

use GDO\Core\GDO;
use GDO\Core\GDO_FileCache;
use GDO\Core\GDT;
use GDO\Core\GDT_JSON;
use GDO\Core\MethodCompletion;
use GDO\UI\TextStyle;

/**
 * Path completion for GDT_Path.
 * Can filter for dirs or for files.
 *
 * @since 7.0.3
 * @author gizmore
 */
final class PathCompletion extends MethodCompletion
{

	protected function gdoTable(): GDO
	{
		# Actually fake and not used!
		return GDO_FileCache::table();
	}

	public function execute(): GDT
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
