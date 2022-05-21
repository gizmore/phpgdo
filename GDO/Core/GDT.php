<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Form\GDT_Validator;

/**
 * The base class for all GDT.
 * It shall not have any attributes at all, to allow lightweight memory types like GDO or GDT_Icon.
 * 
 * A GDT can, or has to, support the following rendering functions / output formats; CLI/JSON/XML/HTML/HEADER/CELL/FORM/CARD/PDF/BINARY/CHOICE/FILTER.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 * @see GDO
 * @see GDT_Pre
 * @see GDT_Field
 * @see WithPHPJQuery
 * @see WithFormAttributes
 */
abstract class GDT
{
	use WithModule;
	
	const EMPTY_ARRAY = [];
	
	###################
	### Instanciate ###
	###################
	/**
	 * Create a GDT instance.
	 * The very basic GDT don't know anything, don't have attributes or much functions.
	 * @param string $name
	 * @return self
	 */
	public static function make(string $name = null) : self
	{
		return new static();
	}
	
	public function blankData() : array
	{
		return self::EMPTY_ARRAY;
	}
	
	#############
	### Debug ###
	#############
	public static bool $GDT_DEBUG = false;
	public static int  $GDT_COUNT = 0;
	
	/**
	 * For the performance counter to work, you have to make sure the constructor chain works.
	 */
	protected function __construct()
	{
		$this->afterLoaded();
	}
	
	public function __wakeup()
	{
		$this->afterLoaded();
	}
	
	private function afterLoaded()
	{
		self::$GDT_COUNT++;
		if (self::$GDT_DEBUG)
		{
			$this->logDebug();
		}
	}
	
	private function logDebug()
	{
		Logger::log('gdt', sprintf('%d: %s', self::$GDT_COUNT, get_class($this)));
		if (self::$GDT_DEBUG > 1)
		{
			Logger::log('gdt', Debug::backtrace('Backtrace GDT allocation', false));
		}
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
	/**
	 * Validation is a great experience in GDOv7.
	 * @TODO make validate use $var instead of $value. Or maybe make a second func validateVar.
	 *
	 * Almost all GDT have a quite decent validator. There is also a GDT to top that; The GDT_Validator.
	 * This GDT parameterizes the target GDT to validate, the value to validate, and the form to check for related fields.
	 * To indicate an error return false. Please use $gdt->error() to make the field in question blink and noting your error
	 * description.
	 *
	 * The GDT_Field class only has a validator algorithm for notNull checks. "You need to fill out this field."
	 * GDT_String takes care of almost 65% of the rest of the input validation. Regex, Lengths, Charset, NotNull, Uniqueness.
	 * The rest is datetime and numbers. Then you almost got all validations figured out for free by object orientated
	 * programming paradigms.
	 * Well, to indicate an error to the form, you call an error method on the faulty GDT and give it an error message; "Your
	 * input needs to be at least 2 chars in length.".
	 * The UI is indicating the faulty field. Animations possibly help in identifying the problem.
	 *
	 * @example where the terms of service have to be clicked.
	 * @example $gdt = GDT_Form::$CURRENT->getField('tos'); $tos = $gdt->getVar() return $tos === true ? true : $gdt->error('You
	 *          need to acknowledge this checkbox and read the privacy guidelines first.');
	 * @example return $value ? true : $gdt->error('err_tos_needs_to_be_truthy');
	 *
	 *          The target $gdt field is an argument as well as the $value and the $form, if you really have to add validation
	 *          rules.
	 *
	 * @see GDT_Int for the integer validator.
	 * @see GDT_String for the string validator.
	 * @see GDT_Validator which is needed rarely. An example is the IP check in Register.
	 * @see GDT_Select
	 * @see GDT_ComboBox
	 * @see GDT_Enum
	 *
	 * @param mixed $value
	 * @return boolean
	 */
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
	public function hasName() : bool
	{
		return false;
	}
	
	public function getName() : ?string
	{
		return null;
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
	
	public function getInput()
	{
		return null;
	}
	
	public function getVar() : ?string
	{
		return null;
	}
	
	public function getValue()
	{
		return null;
	}
	
	public function hasFields() : bool
	{
		return false;
	}
	
	public function getFields() : array
	{
		return self::EMPTY_ARRAY;
	}
	
	public function gdo(GDO $gdo = null) : self
	{
		return $this;
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
	
	public function input($input = null) : self
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
	public function inputToVar(string $input=null) : ?string
	{
		if ($input === null)
		{
			return null;
		}
		$input = trim($input);
		return $input === '' ? null : $input;
	}
	
	public function toVar($value) : ?string
	{
		return $value;
	}
	
	public function toValue(string $var = null)
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

if (def('GDT_GDO_DEBUG', false))
{
	GDT::$GDT_DEBUG = true;
	Logger::log('gdt', '--- NEW RUN ---');
	Logger::log('gdo', '--- NEW RUN ---');
}
