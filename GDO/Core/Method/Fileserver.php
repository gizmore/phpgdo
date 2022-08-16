<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\Date\Time;
use GDO\Util\FileUtil;
use GDO\Core\Application;
use GDO\Net\GDT_Url;
use GDO\Core\GDO_FileCache;

/**
 * Serve a static file from the webserver.
 * Might be forbidden if it's an asset from the /GDO/ folder.
 * Might be forbidden if it's a dotfile.
 * 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
final class Fileserver extends Method
{
	public function isTrivial() : bool { return false; }
	
	public function gdoParameters() : array
	{
		return [
			GDT_Url::make('url')->allowExternal(false),
		];
	}
	
	public function execute()
	{
		$url = $this->gdoParameterVar('url', false);
		$url = ltrim($url, '/');
		
		# Deny by asset rule?
		if (!Module_Core::instance()->checkAssetAllowed($url))
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}

		# Deny PHP source
		$type = FileUtil::mimetype($url);
		if ($type === 'text/x-php')
		{
			return NotAllowed::make()->executeWithInputs($this->inputs);
		}
		
		# Try cached or serve
		$last_modified_time = filemtime($url);
		# @TODO: Cache etag-md5 via modified time
		$etag = $this->md5_file($url, $last_modified_time);
		hdr("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		hdr("Etag: $etag");
		hdr("Expires: ".gmdate("D, d M Y H:i:s", $last_modified_time + Time::ONE_MONTH)." GMT");
		
		$app = Application::$INSTANCE;
		
		# cache hit
		if (@strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
			trim((string)@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
		{
			hdrc('HTTP/1.1 304 Not Modified');
			$app->timingHeader();
			die(0);
		}
		
		# 200 - serve
		hdr('Content-Type: '.$type);
		hdr('Content-Size: '.filesize($url));
		$app->timingHeader();
		readfile($url);
		die(0);
	}
	
	/**
	 * @TODO: implement an md5 cache for the fs.
	 */
	private function md5_file(string $path, int $last_modified_time) : string
	{
		return GDO_FileCache::md5For($path, $last_modified_time);
	}
	
}
