<?php
namespace GDO\Core;

/**
 * Add instance capabilities.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
trait WithInstance
{
	private static self $INSTANCE;
	
	public static function instance() : self
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = self::make();
		}
		return self::$INSTANCE;
	}
	
}
