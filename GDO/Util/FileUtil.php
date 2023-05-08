<?php
declare(strict_types=1);
namespace GDO\Util;

use FilesystemIterator;
use GDO\Core\Debug;
use GDO\Core\GDO_Exception;
use GDO\Core\GDT_Float;
use GDO\Core\Logger;
use GDO\File\Module_File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * File system utilities which are too common for Module_File.
 *
 * @version 7.0.3
 * @since 6.0.0
 * @author gizmore
 * @see Module_File
 */
final class FileUtil
{

	##############
	### Basics ###
	##############
	/**
	 * Create a dir, do not crash, but rather just log errors.
	 * Return back the parameter.
	 */
	public static function createdDir(string $path): string
	{
		try
		{
			self::createDir($path);
		}
		catch (GDO_Exception $ex)
		{
			Debug::debugException($ex, false);
		}
		return $path;
	}

	/**
	 * Try to create a directory, but crash with exception on failure.
	 * @throws GDO_Exception
	 */
	public static function createDir(string $path): true
	{
		if (self::isDir($path))
		{
			if (!is_writeable($path))
			{
				throw new GDO_Exception('err_cannot_write', [html($path)]);
			}
		}
		elseif (is_file($path))
		{
			throw new GDO_Exception('err_cannot_write', [html($path)]);
		}
		elseif (!mkdir($path, GDO_CHMOD, true))
		{
			throw new GDO_Exception('err_cannot_write', [html($path)]);
		}
		return true;
	}

	/**
	 * Check if a dir exists and is readable.
	 */
	public static function isDir(string $filename): bool
	{
		return is_dir($filename) && is_readable($filename);
	}

	public static function createFile(string $path): bool
	{
		if (!self::isFile($path))
		{
			if (!touch($path))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if a file is a file and readable.
	 */
	public static function isFile(string $filename): bool
	{
		return is_file($filename) && is_readable($filename);
	}

	/**
	 * Turn a string into a filec stream.
	 */
	public static function openString(string $string)
	{
		return fopen("data://text/plain, {$string}", 'r');
	}

	/**
	 * Copy a file.
	 */
	public static function copy(string $src, string $dest): bool
	{
		try
		{
			$destDir = Strings::rsubstrTo($dest, '/');
			self::createDir($destDir);
			return copy($src, $dest);
		}
		catch (GDO_Exception $ex)
		{
			Logger::logException($ex);
		}
		return false;
	}

	################
	### Platform ###
	################
	/**
	 * Convert a unix path to a windows path or vice versa.
	 * Just use Linux everywhere.
	 */
	public static function path(string $path): string
	{
		return str_replace('\\', '/', $path);
	}

	###############
	### Dirsize ###
	###############
	/**
	 * Get the size of a folder recursively.
	 */
	public static function dirsize(string $path): int
	{
		$bytes = 0;
		$path = realpath($path);
		if (self::isDir($path))
		{
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file)
			{
				$bytes += $file->getSize();
			}
		}
		return $bytes;
	}

	/**
	 * Scandir without '.' and '..'.
	 *
	 * @return string[] pathes
	 */
	public static function scandir(string $dir): array
	{
		return array_slice(scandir($dir), 2);
	}

	##############
	### Remove ###
	##############

	public static function removedFile(string $path): bool
	{
		try
		{
			return self::removeFile($path);
		}
		catch (GDO_Exception $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}


	/**
	 * Delete a single file.
	 *
	 * @throws GDO_Exception
	 */
	public static function removeFile(string $path): bool
	{
		if (is_file($path))
		{
			if (!unlink($path))
			{
				throw new GDO_Exception('err_delete_file', [$path]);
			}
		}
		elseif (is_dir($path))
		{
			throw new GDO_Exception('err_file_is_dir', [$path]);
		}
		// Not there!
		return true;
	}

	public static function removedDir(string $dir): bool
	{
		try
		{
			return self::removeDir($dir);
		}
		catch (GDO_Exception $ex)
		{
			Debug::debugException($ex);
			return false;
		}
	}

	/**
	 * Remove a dir recursively, file by file.
	 *
	 * @throws GDO_Exception
	 */
	public static function removeDir(string $dir): true
	{
		if (is_dir($dir))
		{
			$objects = self::scandir($dir);
			foreach ($objects as $object)
			{
				$obj = "{$dir}{$object}";
				if (is_dir($obj))
				{
					return self::removeDir("{$obj}/");
				}
				elseif (!unlink($obj))
				{
					throw new GDO_Exception('err_delete_file', [$obj]);
				}
			}
			if (!rmdir($dir))
			{
				throw new GDO_Exception('err_delete_dir', [$dir]);
			}
		}
		elseif (is_file($dir))
		{
			throw new GDO_Exception('err_delete_file_is_dir', [$dir]);
		}
		return true;
	}

	################
	### Filesize ###
	################
	/**
	 * Convert bytes to human filesize like "12.29kb".
	 *
	 * @example humanFilesize(12288, 1000, 3); # => 12.288kb
	 */
	public static function humanFilesize(int $bytes, int $factor = 1024, int $digits = 2): string
	{
		$txt = self::getTextArray();
		$i = 0;
		$rem = '0';
		$sbytes = (string) $bytes;
		$sfactor = (string) $factor;
		while (bccomp($sbytes, $sfactor) >= 0)
		{
			$rem = bcmod($sbytes, $sfactor);
			$sbytes = bcdiv($sbytes, $sfactor);
			$i++;
		}
		if ($i === 0)
		{
			return sprintf('%s %s', $sbytes, $txt[$i]);
		}
		$var = floatval($sbytes) + (floatval($rem) / floatval($factor));
		return GDT_Float::displayS((string)$var, $digits) . ' ' . $txt[$i];
	}

	private static function getTextArray(): array
	{
		return t('_filesize');
	}

	/**
	 * Converts a human filesize to bytes as integer.
	 *
	 * @example humanToBytes("12kb") => 12288
	 */
	public static function humanToBytes(string $s): int
	{
		$txt = self::getTextArray();
		foreach ($txt as $pow => $b)
		{
			if ($pow > 0)
			{
				if (stripos($s, $b) !== false)
				{
					$mul = preg_replace('/[^.0-9]/', '', $s);
					return (int)bcmul($mul, bcpow('1024', (string)$pow));
				}
			}
		}
		return (int) $s;
	}

	#########################
	### Merge Directories ###
	#########################
	/**
	 * Merge two directories recursively.
	 * Used in TBS Importer. Only *chuckles*
	 */
	public static function mergeDirectory(string $source, string $target): bool
	{
		try
		{
			Filewalker::traverse($source, null, function ($entry, $fullpath) use ($source, $target)
			{
				$newpath = str_replace($source, $target, $fullpath);
				self::createDir(Strings::rsubstrTo($newpath, '/'));
				copy($fullpath, $newpath);
			});
			return true;
		}
		catch (\Throwable $ex)
		{
			Logger::logException($ex);
			return false;
		}
	}

	############
	### MIME ###
	############
	public static function mimetype(string $path): string
	{
		$suffix = substr($path, -3);
		switch ($suffix)
		{
			case '.js':
				return 'text/javascript';
			case 'css':
				return 'text/css';
			case 'php':
				return 'text/x-php';
			case '.md':
				return 'text/markdown';
			default:
				return mime_content_type($path);
		}
	}

	##############
	### Sanity ###
	##############
	/**
	 * Remove invalid characters from a filename.
	 */
	public static function saneFilename(string $filename): string
	{
		return str_replace(['/', '\\', '$', ':', '!', '^', '~'], '#', $filename);
	}

	################
	### LastLine ###
	################
	/**
	 * Get the last line of a file.
	 */
	public static function lastLine(string $filename): string
	{
		try
		{
			$fh = fopen($filename, 'r');
			return self::_lastLine($fh);
		}
		finally
		{
			if ($fh)
			{
				fclose($fh);
			}
		}
	}

	/**
	 * Get the last line from a filehandle.
	 * Destroys seek.
	 * @param resource $fh
	 */
	public static function _lastLine($fh): string
	{
		$line = '';

		$cursor = -1;
		fseek($fh, $cursor, SEEK_END);
		$char = fgetc($fh);

		/**
		 * Trim trailing newline chars of the file
		 */
		while ($char === "\n" || $char === "\r")
		{
			fseek($fh, $cursor--, SEEK_END);
			$char = fgetc($fh);
		}

		/**
		 * Read until the start of file or first newline char
		 */
		while ($char !== false && $char !== "\n" && $char !== "\r")
		{
			/**
			 * Prepend the new char
			 */
			$line = $char . $line;
			fseek($fh, $cursor--, SEEK_END);
			$char = fgetc($fh);
		}

		return $line;
	}

	public static function getContents(string $filename): ?string
	{
		return self::isFile($filename) ? file_get_contents($filename) : null;
	}

}
