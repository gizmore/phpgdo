<?php
namespace GDO\Core;

use GDO\DB\Query;

/**
 * The base class for all GDT.
 * It shall not have any attributes at all, to allow lightweight memory types like GDO or GDT_Label.
 * 
 * A GDT can support these rendering functions; CLI/JSON/XML/HTML/HEADER/CELL/FORM/CARD/PDF/BINARY/CHOICE/FILTER.
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
	const RENDER_CLI = 0;
	const RENDER_HTML = 1;
	const RENDER_FORM = 2;
	const RENDER_CELL = 3;
	const RENDER_FILTER = 4;
	const RENDER_HEADER = 5;
	const RENDER_CARD = 6;
	const RENDER_XML = 7;
	const RENDER_JSON = 8;
	const RENDER_BINARY = 9;
	const RENDER_PDF = 10;
	
	public static int $RENDER_MODE = self::RENDER_HTML;
	
	public function render() : string { return $this->renderGDT(); }
	
	public function renderGDT() : string
	{
		switch (self::$RENDER_MODE)
		{
			case self::RENDER_CLI: return $this->renderCLI();
			case self::RENDER_HTML: return $this->renderHTML();
			case self::RENDER_FORM: return $this->renderForm();
			case self::RENDER_CELL: return $this->renderHTML();
			case self::RENDER_FILTER: return $this->renderFilter('');
			case self::RENDER_HEADER: return $this->renderHeader();
			case self::RENDER_CARD: return $this->renderCard();
			case self::RENDER_XML: return $this->renderXML();
			case self::RENDER_JSON: return $this->renderJSON();
			case self::RENDER_BINARY: return $this->renderBinary();
			case self::RENDER_PDF: return $this->renderPDF();
			default: return '';
		}
	}
	
	public function renderCLI() : string { return $this->render(); }
	public function renderXML() : string { return $this->render(); }
	public function renderPDF() : string { return $this->renderHTML(); }
	public function renderCard() : string { return $this->renderHTML(); }
	public function renderCell() : string { return $this->renderHTML(); }
	public function renderForm() : string { return $this->renderHTML(); }
	public function renderHTML() : string { return ''; }
	public function renderJSON() { return $this->renderCLI(); }
	public function renderBinary() : string {}
	public function renderChoice() : string { return $this->renderHTML(); }
	public function renderFilter($f) : string {}
	public function renderHeader() : string {}
	
	public function displayVar(string $var=null) : string
	{
		return $var ? html($var) : '';
	}
	
	public function renderMode(int $mode)
	{
		$old = self::$RENDER_MODE;
		self::$RENDER_MODE = $mode;
		$result = $this->render();
		self::$RENDER_MODE = $old;
		return $result;
	}
	
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
	public function isOrderDefaultAsc() : bool { return true; }
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
	
	/**
	 * Setup the default label. None by default.
	 * @return self
	 */
	public function defaultLabel() : self
	{
		return $this;
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
		return '';
	}
	
	##################
	### DEPRECATED ### backwards compat :(
	##################
	/**
	 * @deprecated SLOW AND FUCK AND NEW
	 * @return string
	 */
	public function getRequestVar($name, $default=null, $bla=null)
	{
		if ($input = $this->getInput())
		{
			return $this->inputToVar($input);
		}
		return $default;
	}
	
}
