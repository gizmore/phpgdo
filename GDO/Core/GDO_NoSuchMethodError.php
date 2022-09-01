<?php
namespace GDO\Core;

final class GDO_NoSuchMethodError extends GDO_Error
{
	public GDO_Module $module;
	public string $methodName;
	
	public function __construct(GDO_Module $module, string $methodName)
	{
		parent::__construct('err_unknown_method', [$module->gdoHumanName(), html($methodName)]);
		$this->module = $module;
		$this->methodName = $methodName;
	}

}
