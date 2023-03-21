<?php
namespace GDO\Form;

use GDO\Core\GDT_String;

/**
 * A fake field that may *not* be filled out.
 * This is kinda additional captcha for havoc bots.
 *
 * @version 7.0.1
 * @author gizmore
 */
final class GDT_Trap extends GDT_String
{

	public function defaultLabel(): self
	{
		return $this->label('trap');
	}

	public function renderForm(): string
	{
		$html = parent::renderForm();
		return "<div class=\"dc\">$html</div>";
	}

	public function validate($value): bool
	{
		if ($value === null)
		{
			return true;
		}
		return $this->error('err_trap');
	}

	public function plugVars(): array
	{
		return [[$this->name => null]];
	}

}
