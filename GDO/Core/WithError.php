<?php
namespace GDO\Core;

/**
 * Adds error annotations to GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 */
trait WithError
{
	public string $error;
	public array $errorArgs;
	public string $errorRaw;
	
	public function error(string $key, array $args=null) : bool
	{
		$this->error = $key;
		$this->errorArgs = $args;
		$this->errorRaw = null;
		return false;
	}
	
	public function errorRaw(string $message) : bool
	{
		$this->error = $this->errorArgs = null;
		$this->errorRaw = $message;
		return false;
	}
	
	public function hasError() : bool
	{
		return $this->error || $this->errorRaw;
	}
	
	public function displayError() : string
	{
		if ($this->errorRaw)
		{
			return $this->errorRaw;
		}
		if ($this->error)
		{
			return t($this->error, $this->errorArgs);
		}
	}
	
}
