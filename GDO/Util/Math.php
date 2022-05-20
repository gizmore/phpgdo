<?php
namespace GDO\Util;
/**
 * Math utility.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 1.0.0
 */
final class Math
{
	#############
	### Clamp ###
	#############
	public static function clampInt(int $number, int $min=null, int $max=null) : int
	{
		if ( ($min !== null) && ($number < $min) ) return $min;
		if ( ($max !== null) && ($number > $max) ) return $max;
		return $number;
	}
	
	public static function clampFloat(float $number, float $min=null, float $max=null) : float
	{
		if ( ($min !== null) && ($number < $min) ) return $min;
		if ( ($max !== null) && ($number > $max) ) return $max;
		return $number;
	}

}
