<?php
declare(strict_types=1);
namespace GDO\Core;

use Exception;
use GDO\CLI\CLI;
use GDO\Language\Trans;
use GDO\UI\Color;
use GDO\UI\GDT_Error;
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

	final public const GET_OK_CODE = 200;

	final public const POST_OK_CODE = 201;

	final public const PERM_ERROR_CODE = 403;

	final public const GDT_ERROR_CODE = 409;

	final public const GDO_ERROR_CODE = 500;

	final public const DB_ERROR_CODE = 500;



	public function __construct(string $key = null, array $args = null, int $code = self::GDT_ERROR_CODE, Throwable $previous = null)
	{
		$message = t($key, $args);
		parent::__construct($message, $code, $previous);
		Application::setResponseCode($code);
		hdr('X-GDO-ERROR: ' . GDT_Error::displayHeaderText($message));
	}


	public static function raw(string $message, int $code = self::GDO_ERROR_CODE, Throwable $previous = null): static
	{
		return new static('%s', [$message], $code, $previous);
	}

}
