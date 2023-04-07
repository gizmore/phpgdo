<?php
namespace GDO\CLI\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\UI\GDT_Repeat;

/**
 * Glue strings together.
 *
 * @version 7.0.0
 * @author gizmore
 */
final class Concat extends Method
{

	public function getCLITrigger(): string
	{
		return 'concat';
	}

	public function gdoParameters(): array
	{
		return [
			GDT_String::make('glue')->initial(''),
			GDT_Repeat::makeAs('text', GDT_String::make()),
		];
	}

	public function execute(): GDT
	{
		$strings = $this->gdoParameterValue('text');
		$glue = $this->gdoParameterVar('glue');
		$glued = implode($glue, $strings);
		return GDT_String::make()->var($glued);
	}

}
