<?php
namespace GDO\Core;

/**
 * A tuple is used as a response value.
 * It inflattens, that means:
 * In HTML it does not get wrapped in a gdt-container.
 * In JSON it does inflatten. instead of response => values you will just get values.
 *
 * @version 7.0.1
 * @since 7.0.0
 * @author gizmore
 */
class GDT_Tuple extends GDT
{

	use WithError;
	use WithFields;

	public function addField(?GDT $gdt, GDT $after = null, bool $last = true): self
	{
		if ($gdt === null)
		{
			return $this;
		}
		if ($gdt instanceof self)
		{
			return $this->addFields(...array_values($gdt->getFields()));
		}
		return $this->addFieldB($gdt);
	}

	protected function addFieldB(GDT $gdt, GDT $after = null, bool $last = true): self
	{
		$this->addFieldA($gdt, $after, $last);
		if ($gdt->hasError())
		{
			$this->error('err_method_failed', [$gdt->renderError()]);
		}
		return $this;
	}

	public function hasError(): bool
	{
		$error = $this->withFields(function (GDT $gdt)
		{
			return $gdt->hasError();
		}, true);
		return $error || parent::hasError();
	}

}
