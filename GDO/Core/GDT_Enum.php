<?php
namespace GDO\Core;

/**
 * 
 * @author gizmore
 *
 */
class GDT_Enum extends GDT_Select
{
	protected function __construct()
	{
		parent::__construct();
	}

	public array $enumValues;
	public function enumValues(string...$enumValues) : self
	{
		$this->choices(array_combine($enumValues, $enumValues));
		$this->enumValues = $this->choices;
		return $this;
	}

	################
	### DB Field ###
	################
	public function gdoColumnDefine() : string
	{
		$values = implode(',', array_map(array('GDO\Core\GDO', 'quoteS'), $this->enumValues));
		return "{$this->identifier()} ENUM ($values) CHARSET ascii COLLATE ascii_bin {$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

}
