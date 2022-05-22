<?php
namespace GDO\Core;

/**
 * Adds error annotations to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.1.0
 */
trait WithError
{
	public string $errorRaw;
	public string $error;
	public array $errorArgs;
	
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
		return isset($this->error) || isset($this->errorRaw);
	}
	
	public function renderError() : string
	{
		if (isset($this->errorRaw))
		{
			return $this->errorRaw;
		}
		if (isset($this->error))
		{
			return t($this->error, $this->errorArgs);
		}
		return '';
	}
	
	/**
	 * Render error message as html form field error annotation.
	 */
	public function htmlError() : string
	{
		return $this->hasError() ?
			('<div class="gdo-form-error">' . $this->renderError() . '</div>') :
			'';
	}
	
}
