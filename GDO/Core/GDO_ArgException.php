<?php
namespace GDO\Core;

/**
 * Invalid argument exception.
 * 
 * @author gizmore
 */
final class GDO_ArgException extends GDO_Error
{
	public function __construct(GDT $field, $code=GDO_Exception::DEFAULT_ERROR_CODE) {
		$f = $field->hasName() ? $field->getName() : $field->gdoHumanName();
		$e = $field->renderError();
		parent::__construct('err_parameter', [$f, $e], $code);
	}
	
}
