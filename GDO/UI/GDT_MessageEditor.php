<?php
namespace GDO\UI;

use GDO\Core\GDT_Select;

/**
 * A select for a message editor.
 * Used as a user setting in module UI.
 * 
 * @author gizmore
 */
final class GDT_MessageEditor extends GDT_Select
{
	public function defaultLabel() : self { return $this->label('editor'); }
	
	protected function __construct()
	{
		parent::__construct();
// 		$this->initial(GDT_Message::$EDITOR_NAME);
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

