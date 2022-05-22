<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDT_Template;

/**
 * Default method that simply loads a template.
 * Uses gdoParameters to populate template vars.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.4.0
 */
abstract class MethodPage extends Method
{
	public function execute()
	{
		$name = strtolower($this->gdoShortName());
		return $this->pageTemplate($name);
	}
	
	protected function pageTemplate($name) : GDT_Template
	{
		return $this->templatePHP("page/{$name}.php",
			$this->getTemplateVars());
	}
	
	protected function getTemplateVars()
	{
		$tVars = [];
		foreach ($this->gdoParameters() as $param)
		{
			$tVars[$param->name] = $this->gdoParameterValue($param->name);
		}
		return $tVars;
	}

}
