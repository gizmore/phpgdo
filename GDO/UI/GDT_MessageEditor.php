<?php
namespace GDO\UI;

use GDO\Core\GDT_Select;

final class GDT_MessageEditor extends GDT_Select
{
	public function defaultLabel() : self { return $this->label('editor'); }
	
	protected function __construct()
	{
		parent::__construct();
		$this->notNull();
	}
	
	public function getChoices()
	{
		$decoders = [];
		foreach (array_keys(GDT_Message::$DECODERS) as $name)
		{
			$decoders[$name] = $name;
		}
		return $decoders;
	}
	
}

