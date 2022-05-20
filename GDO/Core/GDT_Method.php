<?php
namespace GDO\Core;

/**
 * A GDT_Method holds a Method and inputs to bind.
 * An input s either a string or a GDT_Method.
 * A method saves it response [WithResult.php]()
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.0
 */
class GDT_Method extends GDT
{
	use WithName;
	use WithFields;
	use WithEnvironment;
	
	public function execute() : GDT
	{
		$this->method->inputs($this->inputs);
		$gdt = $this->changeUser()->method->exec();
		return $this->result($gdt);
	}
	
}
