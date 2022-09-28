<?php
namespace GDO\Util;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GDO\Core\GDT_Float;
use GDO\Core\GDO_Error;
use GDO\Core\Logger;

/**
 * File system utilities which are too common for the bigger file handling module.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 */
final class FileUtil
{
    ##############
    ### Basics ###
    ##############
    /**
     * Check if a dir exists and is readable.
     */
    public static function isDir(string $filename) : bool
    {
    	return is_dir($filename) && is_readable($filename);
    }

    /**
     * Check if a file is a file and readable.
     */
    public static function isFile(string $filename) : bool
    {
    	return is_file($filename) && is_readable($filename);
#    	return stream_resolve_include_path($filename) !== false; IS TOLD TO BE FAST... lies?
    }

	public static function createDir(string $path, bool $throw=true) : bool
	{
		if (self::isDir($path))
		{
			if (!is_writeable($path))
			{
				if ($throw)
				{
					throw new GDO_Error('err_cannot_write', [html($path)]);
				}
				return false;
			}
			return true;
		}
		return mkdir($path, GDO_CHMOD, true);
	}

	public static function createFile(string $path) : bool
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
	 * Copy a file. ???
	 * @deprecated
	 */
	public static function copy(string $src, string $dest) : bool
	{
		$destDir = Strings::rsubstrTo($dest, '/');
		if (self::createDir($destDir))
		{
			return copy($src, $dest);
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
	public static function path(string $path) : string
	{
// 		if (Process::isWindows())
// 		{
// 			return str_replace('/', '\\', $path);
// 		}
// 		else
// 		{
			return str_replace('\\', '/', $path);
// 		}
	}
	
	###############
	### Dirsize ###
	###############
	/**
	 * Get the size of a folder recursively.
	 * @deprecated too slow! @TODO Maybe use cached in Core/Fileserver GDO_FileCache?
	 */
	public static function dirsize(string $path) : int
	{
		$bytes = 0;
		$path = realpath($path);
		if (self::isDir($path))
		{
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file)
			{
				$bytes += $file->getSize();
			}
		}
		return $bytes;
	}
	
    /**
	 * Scandir without '.' and '..'. 
	 * @return string[] pathes
	 */
	public static function scandir(string $dir) : array
	{
		return array_slice(scandir($dir), 2);
	}
	
	##############
	### Remove ###
	##############
	/**
	 * Delete a single file.
	 */
	public static function removeFile(string $path) : bool
	{
		if (is_file($path))
		{
			return unlink($path);
		}
		elseif (is_dir($path))
		{
			throw new GDO_Error('err_delete_file', [html($path)]);
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Remove a dir recursively, file by file.
	 * @deprecated use system(rm -rf) because this is slow.
	 */
	public static function removeDir(string $dir, bool $throw=true) : bool
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
			{
				if ($object !== '.' && $object !== '..')
				{
				    $obj = "{$dir}/{$object}";
					if (is_dir($obj))
					{
						self::removeDir($obj);
					}
					else
					{
					    if (!unlink($obj))
					    {
					    	if ($throw)
					    	{
					    		throw new GDO_Error('err_delete_file', [html($obj)]);
					    	}
					    	Logger::logError(ten('err_delete_file', [html($obj)]));
					        return false;
					    }
					}
				}
			}
			return self::removeDir($dir, $throw);
		}
		elseif (is_file($dir))
		{
			throw new GDO_Error('err_delete_file', [html($dir)]);
		}
		return true;
	}

	################
	### Filesize ###
	################
	/**
	 * Convert bytes to human filesize like "12.29kb".
	 * @example humanFilesize(12288, 1000, 3); # => 12.288kb
	 */
	public static function humanFilesize(int $bytes, int $factor=1024, int $digits=2) : string
	{
		$txt = self::getTextArray();
		$i = 0;
		$rem = '0';
		while (bccomp($bytes, $factor) >= 0)
		{
			$rem = bcmod($bytes, $factor);
			$bytes = bcdiv($bytes, $factor);
			$i++;
		}
		
		if ($i === 0)
		{
		    # empty?
		    return sprintf("%s%s", $bytes, $txt[$i]);
		}
		
		$var = $bytes + ($rem / $factor);
		return GDT_Float::displayS($var, $digits) . $txt[$i];
	}
	
	private static function getTextArray() : array
	{
	    $txt = t('_filesize');
	    if (!is_array($txt))
	    {
	        $txt = [
	            'B',
	            'KB',
	            'MB',
	            'GB',
	            'TB',
	            'PB',
	        ];
	    }
	    return $txt;
	}
	
	/**
	 * Converts a human filesize to bytes as integer.
	 * @example humanToBytes("12kb") => 12288
	 */
	public static function humanToBytes(string $s) : int
	{
	    $txt = self::getTextArray();
	    foreach ($txt as $pow => $b)
	    {
	        if ($pow > 0)
	        {
	            if (stripos($s, $b) !== false)
	            {
	                $mul = preg_replace('/[^\\.0-9]/', '', $s);
	                return (int) bcmul($mul, bcpow(1024, $pow));
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
	 * Used in TBS Importer only *chuckle*
	 */
	public static function mergeDirectory(string $source, string $target) : bool
	{
	    Filewalker::traverse($source, null, function($entry, $fullpath) use ($source, $target) {
	        $newpath = str_replace($source, $target, $fullpath);
	        FileUtil::createDir(Strings::rsubstrTo($newpath, '/'));
	        copy($fullpath, $newpath);
	    });
	    return true;
	}
	
	############
	### MIME ###
	############
	public static function mimetype(string $path) : string
	{
        $suffix = substr($path, -3);
        switch($suffix)
        {
            case '.js': return 'text/javascript';
            case 'css': return 'text/css';
            case 'php': return 'text/x-php';
            case '.md': return 'text/markdown';
            default: return mime_content_type($path);
        }
	}
	
	##############
	### Sanity ###
	##############
	/**
	 * Remove invalid characters from a filename.
	 */
	public static function saneFilename(string $filename) : string
	{
	    return str_replace(['/', '\\', '$', ':'], '#', $filename);
	}
	
	################
	### LastLine ###
	################
	/**
	 * Get the last line of a file.
	 * @param string $filename
	 * @throws \Throwable
	 * @return string
	 */
	public static function lastLine(string $filename) : string
	{
	    try
	    {
    	    $fh = fopen($filename, "r");
            return self::_lastLine($fh);
	    }
	    catch (\Throwable $ex)
	    {
	        throw $ex;
	    }
	    finally
	    {
	        if ($fh)
	        {
	            @fclose($fh);
	        }
	    }
	}
	
	/**
	 * Get the last line from a filehandle.
	 * Destroys seek.
	 * @param resource $fh
	 */
	public static function _lastLine($fh) : string
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
	
}
