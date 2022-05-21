<?php
namespace GDO\Core\Method;

use GDO\Core\Method;
use GDO\Core\Module_Core;
use GDO\Date\Time;
use GDO\File\FileUtil;

/**
 * Serve a static file from the webserver.
 * Might be forbidden if it's an asset from the /GDO/ folder.
 * Might be forbidden if it's a dotfile.
 * 
 * 403 and 404 pages traditionally send an email, optionally.
 * 
 * @author gizmore
 * @version 7.0.1
 */
final class Fileserver extends Method
{
	public function execute()
	{
		$url = (string) $_REQUEST['url'];
		
		Module_Core::instance()->checkAssetAllowance($url);
		
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
			timingHeader();
			die(0);
		}
		
		# 200 - serve
		$type = FileUtil::mimetype($url);
		hdr('Content-Type: '.$type);
		hdr('Content-Size: '.filesize($url));
		timingHeader();
		readfile($url);
		die(0); # no fallthrough!
	}
}
