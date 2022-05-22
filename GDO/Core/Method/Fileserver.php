<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\Date\Time;
use GDO\Util\FileUtil;
use GDO\Core\Application;

/**
 * Serve a static file from the webserver.
 * Might be forbidden if it's an asset from the /GDO/ folder.
 * Might be forbidden if it's a dotfile.
 * 
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class Fileserver extends Method
{
	public function isTrivial() : bool { return false; }
	
	public function execute()
	{
		$url = (string) $_REQUEST['url'];
		
		if (!Module_Core::instance()->checkAssetAllowed($url))
		{
			return NotAllowed::make()->execute();
		}
		
		$last_modified_time = filemtime($url);
		# @TODO: Cache etag-md5 via modified time
		$etag = md5_file($url);
		hdr("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		hdr("Etag: $etag");
		hdr("Expires: ".gmdate("D, d M Y H:i:s", $last_modified_time + Time::ONE_MONTH)." GMT");
		
		# cache hit
		if (@strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
			trim((string)@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag)
		{
			hdrc('HTTP/1.1 304 Not Modified');
			Application::timingHeader();
			die(0);
		}
		
		# 200 - serve
		$type = FileUtil::mimetype($url);
		hdr('Content-Type: '.$type);
		hdr('Content-Size: '.filesize($url));
		Application::timingHeader();
		readfile($url);
		die(0);
	}
}
