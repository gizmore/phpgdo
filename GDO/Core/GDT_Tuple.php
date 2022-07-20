<?php
namespace GDO\Core;

/**
 * A tuple is used as a response value.
 * It inflattens, that means:
 * In HTML it does not get wrapped in a gdt-container.
 * In JSON it does inflatten. instead of response => values you will just get values.
 * 
 * @author gizmore
 * @version 7.0.0
 */
class GDT_Tuple extends GDT
{
	use WithError;
	use WithFields;
	
	public function addField(GDT $gdt)
	{
		if ($gdt instanceof self)
		{
			return $this->addFields(...array_values($gdt->getFields()));
		}
		return $this->addFieldB($gdt);
	}
	
	protected function addFieldB(GDT $gdt) : self
	{
		$this->addFieldA($gdt);
		if ($gdt->hasError())
		{
			$this->error('err_method_failed', [$gdt->renderError()]);
		}
		return $this;
	}
	
}
