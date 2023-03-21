<?php
namespace GDO\Core;

use GDO\Net\GDT_Url;
use GDO\Util\Strings;

/**
 * An URL to index.php URL mapping.
 *
 * @version 7.0.2
 * @since 7.0.1
 * @author gizmore
 */
final class GDO_SEO_URL extends GDO
{

	public static function addRoute(string $path, string $url): self
	{
		return self::blank([
			'su_file' => $path,
			'su_url' => $url,
		])->replace();
	}

	###########
	### API ###
	###########

	public static function removeRoute(string $path): bool
	{
		return self::table()->deleteWhere('su_file=' . quote($path)) === 1;
	}

	public static function getSEOUrl(string $file): ?string
	{
		$cache = self::table()->allCached();
		foreach ($cache as $gdo)
		{
			if ($gdo->gdoVar('su_file') === $file)
			{
				return $gdo->gdoVar('su_url');
			}
		}
		return null;
	}

	public static function getSEOMethod(string $url): ?Method
	{
		$query = Strings::substrFrom($url, '?', '');
		$result = [];
		parse_str($query, $result);
	}

	public function gdoColumns(): array
	{
		return [
			GDT_String::make('su_file')->unique()->primary()->ascii()->caseS(),
			GDT_Url::make('su_url')->allowInternal(),
			GDT_CreatedAt::make('su_created'),
			GDT_CreatedBy::make('su_creator'),
		];
	}

}
