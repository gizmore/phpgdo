<?php
namespace gizmore;
/**
 * Directory utility for free. (DUFF)
 * It traverses a dir with callbacks.
 * 
 * @todo Make filewalker return early on truthy callback. Optionally, of course.
 * @todo Make filewalker use ...$args instead of $args
 * 
 * @link https://phpgdo.com/php-filewalker
 * @link https://github.com/gizmore/php-filewalker
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 */
final class Filewalker
{
	const MAX_RECURSION = 256;
	
	/**
	 * Callback definition dummy.
	 */
	public static function filewalker_stub(string $entry, string $fullpath, array $args=null)
	{
	}
	
	public static function traverse(string $path, string $pattern=null, callable $callback_file=null, callable $callback_dir=null, int $recursive=self::MAX_RECURSION, $args=null)
	{
		if (is_array($path))
		{
			foreach ($path as $_path)
			{
				self::traverse($_path, $pattern, $callback_file, $callback_dir, $recursive, $args);
			}
			return;
		}
		
		$path = rtrim($path, '/\\');
		
		# Readable?
		if (!($dir = @dir($path)))
		{
			return false;
		}
		
		$dirstack = [];
		$filestack = [];
		while ($entry = $dir->read())
		{
			$fullpath = $path.'/'.$entry;
			if ( (strpos($entry, '.') === 0) ) # || (!is_readable($fullpath)) )
			{
				continue;
			}
			
			if (is_dir($fullpath))
			{
				$dirstack[] = [$entry, $fullpath];
			}
			elseif (is_file($fullpath))
			{
			    if ($pattern)
			    {
			        if (!preg_match($pattern, $entry))
			        {
			            continue;
			        }
			    }
			    $filestack[] = array($entry, $fullpath);
			}
		}
		$dir->close();
		
		usort($filestack, function($a, $b) {
			if (is_numeric($a[0]) && is_numeric($b[0]))
			{
				return $a[0] - $b[0];
			}
			return strcasecmp($a[0], $b[0]);
		});
		
		if ($callback_file)
		{
    		foreach ($filestack as $file)
    		{
    			call_user_func($callback_file, $file[0], $file[1], $args);
    		}
		}
		
		usort($dirstack, function($a, $b){ return strnatcasecmp($a[0], $b[0]); });

	    if ($callback_dir)
	    {
	        foreach ($dirstack as $d)
    		{
    			call_user_func($callback_dir, $d[0], $d[1], $args);
		    }
	    }
			
        if ($recursive > 0)
		{
            foreach ($dirstack as $d)
            {
                self::traverse($d[1], $pattern, $callback_file, $callback_dir, $recursive - 1, $args);
            }
		}
	}
	
}
