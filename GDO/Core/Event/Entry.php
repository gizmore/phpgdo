<?php
declare(strict_types=1);
namespace GDO\Core\Event;

use GDO\Core\Application;

/**
 * An entry for the event table.
 *
 * @since  7.0.2
 */
class Entry
{

	public const ONCE = 1;
	public const FOREVER = -1;

	public $callable;
	public ?array $args = null;
	public int $times = self::FOREVER;
	public float $after = 0.0;
	public float $every = 0.0;

	public static function timer(callable $callable, ?array $args, float $every, bool $immediately=true): self
	{
		$entry = new self($callable, $args);
		return $entry->every($every, $immediately);
	}

	public function __construct(callable $callable, array $args = null)
	{
		$this->callable = $callable;
		$this->args = $args;
	}

	public function once(): self
	{
		return $this->times(self::ONCE);
	}

	public function forever(): self
	{
		return $this->times(self::FOREVER);
	}

	public function times(int $times): self
	{
		$this->times = $times;
		return $this;
	}

	public function in(float $seconds): self
	{
		return $this->at(Application::$MICROTIME + $seconds);
	}

	public function at(float $timestamp): self
	{
		$this->after = $timestamp;
		return $this;
	}

	public function every(float $seconds, bool $immediately=true): self
	{
		$this->every = $seconds;
		return $this->immediately($immediately);
	}

	public function immediately(bool $immediately=true): self
	{
		return $this->in($immediately ? 0.0 : $this->every);
	}

	public function isTime(): bool
	{
		return Application::$MICROTIME >= $this->after;
	}

	public function isRepeatAgain(): bool
	{
		return $this->times !== 0;
	}

	public function dispatch(): void
	{
		call_user_func_array($this->callable, $this->args??[]);
		$this->at += $this->every;
		$this->times--;
	}

}
