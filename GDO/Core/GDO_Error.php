<?php
namespace GDO\Core;

/**
 * An exception with translated error message.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDO_Error extends GDO_Exception
{
	public string $key;
	public array $args;
	
	public function __construct(string $key, array $args = null, $code = self::DEFAULT_ERROR_CODE)
	{
		parent::__construct(t($key, $args), $code);
		$this->key = $key;
		$this->args = $args;
	}
	
	public function getMessage() : string
	{
		return t($this->key, $this->args);
	}
	
}
