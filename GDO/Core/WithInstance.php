<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Add instance capabilities.
 *
 * @version 7.0.3
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
			self::$INSTANCE = static::make();
		}
		return self::$INSTANCE;
	}

}
