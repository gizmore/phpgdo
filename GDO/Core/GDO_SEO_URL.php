<?php
namespace GDO\Core;

use GDO\Net\GDT_Url;
use GDO\Util\Strings;

/**
 * An URL to index.php URL mapping.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class GDO_SEO_URL extends GDO
{

	public function gdoColumns(): array
	{
		return [
			GDT_String::make('su_file')->unique()->primary()->ascii()->caseS(),
			GDT_Url::make('su_url')->allowInternal(),
			GDT_CreatedAt::make('su_created'),
			GDT_CreatedBy::make('su_creator'),
		];
	}
	
	###########
	### API ###
	###########
	public static function addRoute(string $path, string $url) : self
	{
		return self::blank([
			'su_file' => $path,
			'su_url' => $url,
		])->replace();
	}
	
	public static function removeRoute(string $path) : bool
	{
		return self::table()->deleteWhere('su_file='.quote($path)) === 1;
	}
	
	public static function getSEOUrl(string $file) : ?string
	{
		return self::table()->select('su_url')->first()
			->where('su_file='.quote($file))
			->exec()->fetchValue();
	}
	
	public static function getSEOMethod(string $url) : ?Method
	{
		$query = Strings::substrFrom($url, '?', '');
		$result = [];
		parse_str($query, $result);
	}

}
