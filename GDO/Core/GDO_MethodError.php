<?php
namespace GDO\Core;

final class GDO_MethodError extends GDO_Exception
{

	public string $methodName;

	public GDO_Module $module;

	public function __construct(string $methodName, GDO_Module $module = null)
	{
		parent::__construct('err_unknown_method', [$methodName]);
		$this->methodName = $methodName;
		$this->module = $module;
	}

}
