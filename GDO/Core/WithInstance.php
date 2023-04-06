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

	public static function instance(): static
	{
		if (!isset(self::$INSTANCE))
		{
			self::$INSTANCE = self::make();
		}
		return self::$INSTANCE;
	}

}
