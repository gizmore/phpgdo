<?php
namespace GDO\Core;

/**
 * The base class for all GDT.
 * It shall not have any attributes at all, to allow lightweight memory types like GDO or GDT_Label.
 * 
 * A GDT can support these rendering functions; CLI/JSON/XML/HTML/HEADER/CELL/FORM/CARD/BINARY/CHOICE/FILTER.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
abstract class GDT
{
	const EMPTY_ARRAY = [];
	
	###################
	### Instanciate ###
	###################
	public static function make(string $name = null) : self
	{
		return new static();
	}
	
	protected function __construct()
	{
	}
	
	##############
	### Render ###
	##############
	public function render() : string {}
	public function renderCLI() : string { return $this->render(); }
	public function renderXML() : string { return $this->render(); }
	public function renderCard() : string { return $this->renderHTML(); }
	public function renderCell() : string { return $this->renderHTML(); }
	public function renderForm() : string { return $this->renderHTML(); }
	public function renderHTML() : string { return $this->render(); }
	public function renderJSON() { return $this->renderCLI(); }
	public function renderBinary() : string {}
	public function renderChoice() : string { return $this->renderHTML(); }
	public function renderFilter($f) : string {}
	
	###################
	### Permissions ###
	###################
	public function isHidden() : bool { return false; }
	public function isReadable() : bool { return false; }
	public function isWritable() : bool { return false; }
	public function isSerializable() : bool { return false; }
	
	################
	### Features ###
	################
	public function isOrderable() : bool { return false; }
	public function isSearchable() : bool { return false; }
	public function isFilterable() : bool { return false; }
	
	################
	### Validate ###
	################
	public function validate($value) : bool
	{
		return true;
	}
	
	public function hasError() : bool
	{
		return false;
	}
	
	public function error(string $key, array $args = null) : bool
	{
		return false;
	}
	
	##############
	### Config ###
	##############
	public function configJSON() : array
	{
		return get_object_vars($this);
	}
	
	#########################
	### Bridge for traits ###
	#########################
	public function getName() : string
	{
	}
	
	public function getInput() : string
	{
	}
	
	public function getVar() : string
	{
	}
	
	public function getValue()
	{
	}
	
	public function getFields() : array
	{
		return self::EMPTY_ARRAY;
	}
	
	public function gdo(GDO $gdo) : self
	{
		return $this;
	}
	
	public function htmlVar() : string
	{
		return html($this->getVar());
	}
	
	public function htmlName() : string
	{
	}
	
	public function input(string $input) : self
	{
		return $this;
	}
	
	public function var(string $var) : self
	{
		return $this;
	}
	
	public function value($value) : self
	{
		return $this;
	}
	
	public function isPositional() : bool
	{
		return false;
	}
	
	public function isVirtual() : bool
	{
		return false;
	}
	
	#############
	### Tests ###
	#############
	/**
	 * This is the default input for automagical unit tests.
	 * 
	 * @return string
	 */
	public function plugVar() : string
	{
	}
	
}
