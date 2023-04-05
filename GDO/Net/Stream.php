<?php
namespace GDO\Net;

use GDO\Core\Application;
use GDO\Core\GDT_Response;
use GDO\File\GDO_File;

/**
 * File utility to stream downloads in chunks.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
final class Stream
{

	public static function serve(GDO_File $file, $variant = '', $disposition = true)
	{
		hdr('Content-Type: ' . $file->getType());
		hdr('Content-Size: ' . $file->getSize());
		if ($disposition)
		{
			hdr('Content-Disposition: attachment; filename="' . htmlspecialchars($file->getName()) . '"');
		}
		self::file($file, $variant);
		Application::exit();
		return GDT_Response::make();
	}

	public static function file(GDO_File $file, $variant = '')
	{
		self::path($file->getVariantPath($variant));
	}

	public static function path($path)
	{
		if (Application::$INSTANCE->isUnitTests())
		{
			echo "Sending file: $path\n";
			return '';
		}
		else
		{
			$out = false;
			if (ob_get_level() > 0)
			{
				$out = ob_get_contents();
				ob_end_clean();
			}
			$result = self::_path($path);
			if ($out !== false)
			{
				ob_start();
				echo $out;
			}
			return $result;
		}
	}

	private static function _path($path)
	{
		if ($fh = fopen($path, 'rb'))
		{
			while (!feof($fh))
			{
				echo fread($fh, 1024 * 1024);
				flush();
			}
			fclose($fh);
			return true;
		}
		return false;
	}

	/**
	 * Serve a HTTP range request if desired.
	 *
	 * @param GDO_File $file
	 * @param string $variant
	 */
	public static function serveWithRange(GDO_File $file, $variant = '')
	{
		$die = !Application::$INSTANCE->isUnitTests();
		$size = $length = $file->getSize();
		$start = 0;
		$end = $size - 1;

		$path = $file->getVariantPath($variant);
		$fp = fopen($path, 'rb');

		hdr('Content-type: ' . $file->getType());
		hdr('Accept-Ranges: 0-' . $size);

		if (isset($_SERVER['HTTP_RANGE']))
		{
			$c_start = $start;
			$c_end = $end;

			[, $range] = explode('=', $_SERVER['HTTP_RANGE'], 2);
			if (strpos($range, ',') !== false)
			{
				hdrc('HTTP/1.1 416 Requested Range Not Satisfiable');
				hdr("Content-Range: bytes $start-$end/$size");
				exit;
			}
			if ($range == '-')
			{
				$c_start = $size - substr($range, 1);
			}
			else
			{
				$range = explode('-', $range);
				$c_start = $range[0];
				$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
			}
			$c_end = ($c_end > $end) ? $end : $c_end;
			if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
			{
				hdrc('HTTP/1.1 416 Requested Range Not Satisfiable');
				hdr("Content-Range: bytes $start-$end/$size");
				return;
			}
			$start = $c_start;
			$end = $c_end;
			$length = $end - $start + 1;
			fseek($fp, $start);
			hdrc('HTTP/1.1 206 Partial Content');
		}

		hdr("Content-Range: bytes $start-$end/$size");
		hdr('Content-Length: ' . $length);

		$buffer = 1024 * 8;
		while (!feof($fp) && ($p = ftell($fp)) <= $end)
		{
			if ($p + $buffer > $end)
			{
				$buffer = $end - $p + 1;
			}

			$data = fread($fp, $buffer);
			if ($die)
			{
				echo $data;
				flush();
			}
		}
		fclose($fp);
		if ($die)
		{
			die();
		}
		else
		{
			echo 'Served ' . $file->renderName();
		}
	}

	public static function serveText($content, $fileName)
	{
		hdr('Content-Type: application/octet-stream');
		hdr('Content-Disposition: attachment; filename=' . html($fileName));
		hdr('Expires: 0');
		hdr('Cache-Control: must-revalidate');
		hdr('Pragma: public');
		hdr('Content-Length: ' . strlen($content));
		hdr('Content-Size: ' . strlen($content));
		if (!Application::$INSTANCE->isUnitTests())
		{
			echo $content;
		}
	}

}
