<?php
namespace GDO\Core;

final class GDO_ArgException extends GDO_Error
{
	private GDT $field;
	
	public function __construct(GDT $field) {
	}
	
	public function getMessage() : string
	{
		return t('err_parameter', [$this->field->getName(), $this->field->displayError()]);
	}
}
