<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * A GDT has an invalid input.
 * @version 7.0.3
 */
class GDO_ArgError extends GDO_Exception
{

	public function __construct(GDT $field, \Throwable $previous = null)
	{
		parent::__construct('err_parameter', [$field->gdoHumanName(), $field->renderError()],
			self::GDT_ERROR_CODE, $previous);
	}

}
