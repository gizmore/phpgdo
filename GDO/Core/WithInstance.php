<?php
namespace GDO\Core;

/**
 * Add instance capabilities.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
trait WithInstance
{
	public static self $INSTANCE;
	
	public static function instance(): static
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = self::make();
		}
		return self::$INSTANCE;
	}
	
}
