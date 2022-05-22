<?php
namespace GDO\Core;

final class GDO_ArgException extends GDO_Error
{
	private GDT $field;
	
	public function __construct(GDT $field, $code=GDO_Exception::DEFAULT_ERROR_CODE) {
		parent::__construct('err_parameter', [$this->field->getName(), $this->field->renderError()], $code);
	}
	
}
