<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
trait testy
{

	private array $foo; # I do *not* want to initialize it, for performance reasons.

	public function test()
	{
		unset($this->foo);
//		return isset($this->foo[0]) ? $this->foo[0] : null;
		return $this->foo[1] ?? 'a';
	}

}

abstract class base
{
	use testy;

	private array $foo2; # I do *not* want to initialize it, for performance reasons.

	public function test2()
	{
		unset($this->foo2);
//		return isset($this->foo[0]) ? $this->foo[0] : null;
		return $this->foo2[1] ?? 'a';
	}

}

final class test extends base
{


}

echo (new test())->test();
echo (new test())->test2();
