<?php
namespace GDO\Core;

/**
 * Add instance capabilities.
 *
 * @version 7.0.2
 * @since 7.0.0
 * @author gizmore
 */
trait WithInstance
{

	public static self $INSTANCE;

	public static function instance(): self
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = static::make();
		}
		return self::$INSTANCE;
	}

}
