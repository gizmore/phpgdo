<?php
namespace GDO\Core;

final class GDO_NoSuchMethodError extends GDO_Error
{

	public string $methodName;

	public function __construct(string $methodName)
	{
		parent::__construct('err_unknown_method', [$methodName]);
		$this->methodName = $methodName;
	}

}
