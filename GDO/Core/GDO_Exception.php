<?php
namespace GDO\Core;

/**
 * Base GDOv7 Exception class.
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.3
 */
class GDO_Exception extends \Exception
{
	const DEFAULT_ERROR_CODE = 409;
	
	public function __construct (string $message = null, int $code = self::DEFAULT_ERROR_CODE, \Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		Application::setResponseCode($code);
		Logger::logException($this);
	}

}
