<?php
declare(strict_types=1);
namespace GDO\Core;

use Exception;
use GDO\CLI\CLI;
use GDO\UI\Color;
use GDO\UI\TextStyle;
use Throwable;

/**
 * Base GDOv7 Exception class.
 *
 * @version 7.0.3
 * @since 5.0.3
 * @author gizmore
 */
class GDO_Exception extends Exception
{

	final public const DEFAULT_ERROR_CODE = 409;

	public function __construct(string $message = null, int $code = self::DEFAULT_ERROR_CODE, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
		Application::setResponseCode($code);
		hdr('X-GDO-ERROR: ' . CLI::removeColorCodes(str_replace("\n", ' | ', $message)));
		Logger::logException($this);
	}

	public function renderCLI(): string
	{
		$args = [
			Color::red(get_class($this)),
			TextStyle::italic($this->getMessage()),
		];
		return t('err_exception', $args);
	}

}
