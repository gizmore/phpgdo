<?php
declare(strict_types=1);
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\Method;
use GDO\Core\WithFileCache;

/**
 * Default method that simply loads a template.
 * Uses gdoParameters to populate template vars.
 *
 * @version 7.0.3
 * @since 6.4.0
 * @author gizmore
 */
abstract class MethodPage extends Method
{

	use WithFileCache;

	public function execute(): GDT
	{
		return $this->pageTemplate();
	}

	protected function pageTemplate(): GDT_Template
	{
		return $this->templatePHP(
			$this->getTemplateName(),
			$this->getTemplateVars());
	}

	protected function getTemplateName(): string
	{
		$name = strtolower($this->gdoShortName());
		return "page/{$name}.php";
	}

	protected function getTemplateVars()
	{
		$tVars = [];
		foreach ($this->gdoParameterCache() as $param)
		{
			$tVars[$param->name] = $param->getValue();
		}
		return $tVars;
	}

}
