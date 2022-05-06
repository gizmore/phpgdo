<?php
namespace GDO\Core;

final class Version
{
	public int $major;
	public int $minor;
	public int $patch;
	
	public function __construct($var)
	{
		list($major, $minor, $patch) = explode('.', $var);
		$this->major = intval($major, 10);
		$this->minor = intval($minor, 10);
		$this->patch = intval($patch, 10);
	}
	
}
