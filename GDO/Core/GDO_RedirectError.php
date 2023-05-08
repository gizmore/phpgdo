<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\UI\WithHREF;

/**
 * An exception that want's to redirect after shown.
 *
 * @version 7.0.3
 */
final class GDO_RedirectError extends GDO_Exception
{

	use WithHREF;

	public function __construct(string $key, ?array $args, string $href, int $code = GDO_Exception::GDT_ERROR_CODE, \Throwable $previous = null)
	{
		parent::__construct($key, $args, $code, $previous);
		$this->href = $href;
	}

}
