<?php
namespace GDO\Core;

use GDO\UI\TextStyle;

/**
 * Invalid argument exception.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDO_ArgException extends GDO_Error
{

	public function __construct(GDT $field, int $code = GDO_Exception::DEFAULT_ERROR_CODE)
	{
		$f = $field->hasName() ? $field->getName() : $field->gdoHumanName();
		$e = $field->renderError();
		parent::__construct('err_parameter', [
			TextStyle::bold($f),
			TextStyle::italic($e),
		], $code);
	}

}
