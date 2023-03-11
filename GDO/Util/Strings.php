<?php
namespace GDO\Util;

/**
 * String utility class.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 3.0.0
 */
final class Strings
{
	#########################
	### Substring to/from ###
	#########################
	/**
	 * Get a substring from a string until an occurance of another string.
	 */
	public static function substrTo(string $s, string $to, string $default=null) : ?string
	{
		if (false !== ($index = strpos($s, $to)))
		{
			return substr($s, 0, $index);
		}
		return $default;
	}
	
	/**
	 * Take the portion of a string after/from a portion. You can nibble tokens with that. slow?
	 */
	public static function substrFrom(string $s, string $from, string $default=null) : ?string
	{
		if (false !== ($index = strpos($s, $from)))
		{
			return substr($s, $index + strlen($from));
		}
		return $default;
	}
	
	/**
	 * Get a portion of $s from 0 to last occurance of $to.
	 */
	public static function rsubstrTo(string $s, string $to, string $default=null) : ?string
	{
		if (false !== ($index = strrpos($s, $to)))
		{
			return substr($s, 0, $index);
		}
		return $default;
	}

	/**
	 * Get a portion of $s from the last occurance of $from.
	 */
	public static function rsubstrFrom(string $s, string $from, string $default=null) : ?string
	{
		if (false !== ($index = strrpos($s, $from)))
		{
			return substr($s, $index + strlen($from));
		}
		return $default;
	}
	
	#######################
	### HTML safe nl2br ###
	#######################
	/**
	 * Changes newline to <br/> but only when no tags are open.
	 */
	public static function nl2brHTMLSafe(string $s) : string
	{
	    $s = trim($s, " \r\n\t");
	    $len = strlen($s);
	    $open = 0;
	    $back = '';
	    for ($i = 0; $i < $len; $i++)
	    {
	        $c = $s[$i];
	        switch ($c)
	        {
	        	case '<':
		            $back .= $c;
		            $open++;
		            break;
	        	case '>':
		            $back .= $c;
		            $open--;
		            break;
	        	case "\r":
	        		break;
	        	case "\n":
		            if (!$open)
		            {
		                $back .= "<br/>"; # safe to convert
		            }
		            else
		            {
		                $back .= ' '; # Open tag. use space instead.
		            }
		            break;
		        default:
		            $back .= $c;
		            break;
	        }
	    }
	    return $back;
	}
	
	###################
	### Trim dotted ###
	###################
	public static function dotted(string $text, int $maxlen=50, string $dots='…') : string
	{
		$len = mb_strlen($text);
		if ($len > $maxlen)
		{
			$text = mb_substr($text, 0, $maxlen - 1);
			$text .= $dots;
		}
		return $text;
	}

	###################
	### UTF8 strcmp ###
	###################
	/**
	 * UTF8 capable string comparison.
	 */
	public static function compare(string $a, string $b, bool $caseS=false) : int
	{
		$a = iconv('utf-8', 'ascii//TRANSLIT', $a);
		$b = iconv('utf-8', 'ascii//TRANSLIT', $b);
		return $caseS ? strnatcmp($a, $b) : strnatcasecmp($a, $b);
	}
	
	#####################
	### HTML Shrinker ###
	#####################
	/**
	 * Remove uncessary whitespace from html output.
	 * @deprecated slow
	 */
	public static function shrinkHTML(string $html) : string
	{
		$html = preg_replace('/\s+/', ' ', $html);
		$html = str_replace('> <', '><', $html);
		return $html;
	}
	
	###############
	### Explode ###
	###############
	/**
	 * Explode, trim and remove empty elements.
	 */
	public static function explode(string $string, string $delimiter = ',') : array
	{
		$array = array_map('trim', explode($delimiter, $string));
		return array_filter($array, function(string $s) {
			return $s !== '';
		});
	}
	
}
