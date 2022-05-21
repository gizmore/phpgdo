<?php
namespace GDO\Util;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use GDO\Core\GDT_Float;

/**
 * File system utilities.
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
    public static function isDir(string $filename) : bool
    {
    	return is_dir($filename);
    }

    public static function isFile(string $filename) : bool
    {
    	return stream_resolve_include_path($filename) !== false;
    }

	public static function createDir(string $path) : bool
	{
		if (self::isDir($path) && is_writable($path))
		{
			return true;
		}
		return mkdir($path, GDO_CHMOD, true);
	}

	public static function createFile(string $path) : bool
	{
	    if (!self::isFile($path))
	    {
	        if (!@touch($path))
	        {
	            return false;
	        }
	    }
	    return true;
	}
	
	/**
	 * Copy a file.
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
	
	###############
	### Dirsize ###
	###############
	/**
	 * Get the size of a folder recursively.
	 * 
	 * @deprecated too slow!
	 * @param string $path
	 * @return int
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
	 * @param string $dir
	 * @return string[] pathes
	 */
	public static function scandir(string $dir) : array
	{
		return array_slice(scandir($dir), 2);
	}
	
	/**
	 * Remove a dir recursively, file by file.
	 * 
	 * @deprecated use system(rm -rf)
	 */
	public static function removeDir(string $dir) : bool
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
					    	echo "cannot unlink $obj\n";
					        return false;
					    }
					}
				}
			}
			return rmdir($dir);
		}
		return true;
	}

	################
	### Filesize ###
	################
	/**
	 * Convert bytes to human filesize like "12.29kb".
	 * @example humanFilesize(12288, 1000, 3); # => 12.288kb
	 * @param int $bytes
	 * @param int $factor - 1024 or 1000 should be used
	 * @param int $digits - number of fraction digits
	 * @return string
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
	
// 	#########################
// 	### Merge Directories ###
// 	#########################
// 	/**
// 	 * Merge two directories recursively.
// 	 * @TODO reorder params as $source, $dest
// 	 * @param string $target
// 	 * @param string $source
// 	 */
// 	public static function mergeDirectory(string $target, string $source) : bool
// 	{
// 	    Filewalker::traverse($source, null, function($entry, $fullpath) use ($source, $target) {
// 	        $newpath = str_replace($source, $target, $fullpath);
// 	        FileUtil::createDir(Strings::rsubstrTo($newpath, '/'));
// 	        copy($fullpath, $newpath);
// 	    });
// 	    return true;
// 	}
	
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
        }
	    return mime_content_type($path);
	}
	
	##############
	### Sanity ###
	##############
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
	 * @return string
	 */
	public static function _lastLine($fh)
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
