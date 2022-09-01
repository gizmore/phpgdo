<?php
namespace GDO\Core;

use GDO\UI\Color;
use GDO\UI\TextStyle;

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
	
	public function renderCLI() : string
	{
		$args = [
			Color::red(get_class($this)),
			TextStyle::italic($this->getMessage()),
// 			TextStyle::bold(Debug::shortpath($this->getFile())),
// 			TextStyle::bold($this->getLine()),
		];
		return t('err_exception', $args);
	}

}
