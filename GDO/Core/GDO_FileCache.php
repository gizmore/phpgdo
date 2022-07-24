<?php
namespace GDO\Core;

use GDO\Crypto\GDT_MD5;

/**
 * A cache for static file md5 sums.
 * 
 * @author gizmore
 */
final class GDO_FileCache extends GDO
{
	public function gdoColumns(): array
	{
		return [
			GDT_Name::make('fc_name')->primary()->max(255),
			GDT_MD5::make('fc_md5')->notNull(),
			GDT_UInt::make('fc_mtime')->notNull(),
		];
	}
	
	##############
	### Static ###
	##############
	public static function md5For(string $path, int $last_modified_time) : string
	{
		$table = self::table();
		$path = GDO::quoteS($path);
		if ($file = $table->select()->where("fc_name={$path}")->where("fc_mtime>=$last_modified_time")->first()->exec()->fetchObject())
		{
			return $file->gdoVar('fc_md5');
		}
		return self::newMD5For($path, $last_modified_time);
	}
	
	private static function newMD5For(string $path, int $last_modified_time) : string
	{
		$md5 = md5($path);
		self::blank([
			'fc_name' => $path,
			'fc_md5' => $md5,
			'fc_mtime' => $last_modified_time,
		])->replace();
		return $md5;
	}
	
}
