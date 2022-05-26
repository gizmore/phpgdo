<?php
namespace GDO\Core\Method;

use GDO\UI\MethodPage;
use GDO\Core\GDT_String;
use GDO\Core\GDT;
use GDO\UI\GDT_Error;

final class Error extends MethodPage
{
	public function gdoParameters() : array
	{
		return [
			GDT_String::make('error')->notNull(),
		];
	}
	
	public function execute() : GDT
	{
		return GDT_Error::make()->textRaw($this->gdoParameterValue('error'));
	}
	
}
