<?php
namespace GDO\CLI\Method;

use GDO\Core\Method;
use GDO\Core\GDT_Message;
use GDO\UI\GDT_Label;
use GDO\Core\GDT;
use GDO\Core\ModuleLoader;
use GDO\CLI\CLI;

/**
 * Reply the input back.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 */
final class Ekko extends Method
{
	public function gdoParameters() : array
	{
		return [
			GDT_Message::make('text'),
		];
	}
	
	public function execute()
	{
		$text = $this->gdoParameterVar('text');
		return GDT_Label::make()->labelRaw($text);
	}
	
}
