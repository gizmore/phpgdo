<?php
namespace GDO\Core;

use GDO\DB\Query;

/**
 * The base class for all GDT.
 * It shall not have any attributes at all, to allow lightweight memory types like GDO or GDT_Label.
 * 
 * A GDT can support these rendering functions; CLI/JSON/XML/HTML/HEADER/CELL/FORM/CARD/BINARY/CHOICE/FILTER.
 * 
 * @see GDO
 * @see GDT_Field
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 5.0.0
 */
abstract class GDT
{
	use WithModule;
	
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
		# Make protected so it cannot be used without ::make()
	}
	
	public function blankData() : array 
	{
		return self::EMPTY_ARRAY;
	}
	
	##############
	### Render ###
	##############
	public function render() : string { return ''; }
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
	
	##############
	### Events ###
	##############
	public function gdoBeforeCreate(GDO $gdo, Query $query) : void {}
	public function gdoBeforeRead(GDO $gdo, Query $query) : void {}
	public function gdoBeforeUpdate(GDO $gdo, Query $query) : void {}
	public function gdoBeforeDelete(GDO $gdo, Query $query) : void {}
	
	public function gdoAfterCreate(GDO $gdo) : void {}
	public function gdoAfterRead(GDO $gdo) : void {}
	public function gdoAfterUpdate(GDO $gdo) : void {}
	public function gdoAfterDelete(GDO $gdo) : void {}
	
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
	public function getName() : ?string
	{
		return null;
	}
	
	public function hasName() : bool
	{
		return false;
	}
	
	public function hasInput() : bool
	{
		return false;
	}
	
	public function getInitial() : ?string
	{
		return null;
	}
	
	public function getInput() : ?string
	{
		return null;
	}
	
	public function getVar() : ?string
	{
		return null;
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
		return $this->var($gdo->gdoVar($this->getName()));
	}
	
	public function htmlVar() : string
	{
		return html($this->getVar());
	}
	
	public function htmlName() : string
	{
		return '';
	}
	
	public function htmlID() : string
	{
		return '';
	}
	
	public function htmlAttributes() : string
	{
		return '';
	}
	
	public function input(string $input = null) : self
	{
		return $this;
	}
	
	public function initial(string $initial = null) : self
	{
		return $this;
	}
	
	public function initialValue($value) : self
	{
		return $this->initial($this->toVar($value));
	}
	
	public function var(string $var = null) : self
	{
		return $this;
	}
	
	public function getGDOData() : ?array
	{
		return null;
	}
	
	public function value($value) : self
	{
		return $this;
	}

	public function isRequired() : bool
	{
		return false;
	}
	
	public function isPositional() : bool
	{
		return false;
	}
	
	public function isVirtual() : bool
	{
		return false;
	}
	
	public function isPrimary() : bool
	{
		return false;
	}
	
	public function isUnique() : bool
	{
		return false;
	}
	
	public function gdoCompare(GDO $a, GDO $b) : int
	{
		return 0;
	}
	
	##################
	### Conversion ###
	##################
	public function inputToVar(string $input) : string
	{
		return $input;
	}
	
	public function toVar($value) : ?string
	{
		return $value;
	}
	
	public function toValue(string $var)
	{
		return $var;
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
