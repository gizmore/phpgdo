<?php
namespace GDO\Core\Method;

use GDO\Core\MethodCompletion;
use GDO\Core\GDT_JSON;
use GDO\Core\GDT_EnumNoI18n;
use GDO\UI\TextStyle;

/**
 * Path completion for GDT_Path.
 * Can filter for dirs or for files.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class PathCompletion extends MethodCompletion
{
	public function gdoParameters() : array
	{
		return array_merge(parent::gdoParameters(), [
			GDT_EnumNoI18n::make('check')->initial('any')->enumValues('any', 'is_dir', 'is_file')->notNull(),
		]);
	}
	
	private function getCheckMethod() : string
	{
		return $this->gdoParameterVar('check');
	}
	
	public function execute()
	{
		$query = $this->getSearchTerm();
		$files = glob("{$query}*");
		$check = $this->getCheckMethod();
		$json = [];
		foreach ($files as $file)
		{
			if (!is_readable($file))
			{
				continue;
			}
// 			if ($check !== 'any')
// 			{
// // 				if (!call_user_func($check, $file))
// // 				{
// // 					continue;
// // 				}
// 			}
			$json[] = [
				'id' => $file,
				'text' => $file,
				'display' => TextStyle::underline($file),
			];
		}
		return GDT_JSON::make()->value($json);
	}
	
}
