<?php
declare(strict_types=1);
namespace GDO\Core\Event;

/**
 * Event Table
 *
 * @since 7.0.2
 */
final class Table
{

	public const TIMER_EVENTS = 'time';

	/**
	 * @var Entry[]
	 */
	public static $EVENTS = [];

	public static function register(string $event, Entry $entry): void
	{
		self::$EVENTS[$event] ??= [];
		self::$EVENTS[$event][] = $entry;
	}

	public static function timer(Entry $entry): void
	{
		self::register(self::TIMER_EVENTS, $entry);
	}

	public static function dispatch(string $event): void
	{
		self::$EVENTS[$event] ??= [];
		foreach (self::$EVENTS as $i => $event)
		{
			if ($event->isTime())
			{
				$event->dispatch();
				if (!$event->isRepeatAgain())
				{
					unset(self::$EVENTS[$i]);
				}
			}
		}
	}

}
