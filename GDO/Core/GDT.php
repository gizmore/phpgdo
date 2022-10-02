<?php
namespace GDO\Core;

use GDO\DB\Query;
use GDO\Table\GDT_Filter;
use GDO\UI\TextStyle;
use GDO\Util\Arrays;

/**
 * The base class for all GDT.
 * It shall not have any attributes, to allow lightweight memory types like GDO or GDT_Icon.
 * 
 * A GDT can, better, has to, support the following rendering mode functions a.k.a output formats;
 * 
 * OUTPUT MODES  : CLI / JSON / XML / WEBSITE / PDF / BINARY / GTK
 * HTML SUBMODES : HTML / CARD / FORM / OPTION / LIST /
 * TABLE SUBMODES: ORDER / CELL / THEAD / TFOOT / FILTER
 * 
 * The current rendering mode is stored in @link \GDO\Core\Application
 *  
 * @author gizmore
 * @version 7.0.1
 * @since 5.0.0
 * @see GDO
 * @see GDT_Field
 * @see Application
 */
abstract class GDT
{
	use WithModule;
	
	/**
	 * @var GDT[]
	 */
	const EMPTY_ARRAY = [];
	
	const EMPTY_STRING = ''; # @TODO Does this safe allocs or cycles? i doubt... we can test :)=
	
	###################
	### Instanciate ###
	###################
	/**
	 * Create a GDT instance.
	 * The very basic GDT don't know anything, don't have attributes or much functions.
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
	public static int $GDT_DEBUG = 0; # counts allocations (1) - or can also add a backtrace for every allocation (2).
	public static int $GDT_COUNT = 0; # total allocs
	public static int $GDT_KILLS = 0; # total deallocs
	public static int $GDT_PEAKS = 0; # highest simultan. alive
	
	/**
	 * For the performance counter to work, you have to make sure the constructor chain works.
	 */
	protected function __construct()
	{
		$this->afterLoaded();
	}
	
	public function __destruct()
	{
		self::$GDT_KILLS++;
	}
	
	public function __wakeup()
	{
		$this->afterLoaded();
	}
	
	private function afterLoaded()
	{
		self::$GDT_COUNT++;
		$alive = self::$GDT_COUNT - self::$GDT_KILLS;
		if ($alive > self::$GDT_PEAKS)
		{
			self::$GDT_PEAKS = $alive;
		}
		if (self::$GDT_DEBUG)
		{
			$this->logDebug();
		}
	}
	
	private function logDebug()
	{
		Logger::log('gdt', sprintf('%d: %s', self::$GDT_COUNT, get_class($this)));
		if (self::$GDT_DEBUG >= 2)
		{
			Logger::log('gdt', Debug::backtrace('Backtrace GDT allocation', false));
		}
	}
	
	###########################
	# --- GDT Render Core --- #
	###########################
	/**
	 * @license GDOv7-LICENSE (c)2020-2023 C.B. gizmore@wechall.net
	 * @see Rendern gegen Mobbing.com
	 */
	##############
	### Render ### 19 Render modes
	##############  6 Output formats
	#########################.
	# Various output formats #
	const RENDER_NIL = 0;     # nil
	const RENDER_BINARY = 1;  # websocket (@TODO WS Render Mode)
	const RENDER_CLI = 2;     # plaintext
	const RENDER_PDF = 3;     # pdf (@TODO PDF Render Mode) what is the ultimate solution?
	const RENDER_XML = 4;     # xml (@TODO XML Render Mode)
	const RENDER_JSON= 5;     # json
	const RENDER_GTK = 6;     #### Enjoy!
// 	const RENDER_RESERVED_7 = 7; # who
// 	const RENDER_RESERVED_8 = 8; # knows
// 	const RENDER_RESERVED_9 = 9; # :) ... maybe soap? maybe the new safe JSON?
	# HTML format rendering   #### Your Flight!
	const RENDER_WEBSITE= 10; # <html> page skeleton, html init mode that switches to RENDER_HTML.
	const RENDER_HTML   = 11; # <div> plain html mode
	const RENDER_CARD   = 12; # <div> many ui frameworks use cards...
	const RENDER_LIST   = 13; # <li>
	const RENDER_FORM   = 14; # <form><input>
	const RENDER_OPTION = 15; # <option>
	# HTML table subrendering # # # # # # # # # # # # # # # # # # # # 
	const RENDER_HEADER = 16; # table <th>
	const RENDER_ORDER  = 17; # table <th> order
	const RENDER_FILTER = 18; # table <th> filter
	const RENDER_CELL   = 19; # table <td>
	const RENDER_THEAD  = 20; # table <thead>
	const RENDER_TFOOT  = 21; # table <tfoot>
	
// 	/**
// 	 * Switchable rendering callmap.
// 	 * @var callable[]
// 	 */
// 	public static array $RENDER_CALLMAP = [
// 		[null, 'renderNIL'],
// 		[null, 'renderBinary'],
// 		[null, 'renderXML'],
// 		[null, 'renderJSON'],
// 		[null, 'renderGTK'],
// 		[null, 'renderNIL'],
// 		[null, 'renderNIL'],
// 		[null, 'renderNIL'],
// 		[null, 'renderWebsite'],
// 		[null, 'renderHTML'],
// 		[null, 'renderCard'],
// 		[null, 'renderList'],
// 		[null, 'renderForm'],
// 		[null, 'renderOption'],
// 		[null, 'renderHeader'],
// 		[null, 'renderNIL'],
// 		[null, 'renderFilter'],
// 		[null, 'renderCell'],
// 		[null, 'renderTHead'],
// 		[null, 'renderTFoot'],
// 	];
	
	/**
	 * Call the applications mode rendering method.
	 * Returns an array for JSON.
	 * retuns a string for everything else at the moment.
	 *  
	 * @return string|array
	 */
	protected function renderGDT()
	{
// 		# Now could come a trick :)
// 		# @TODO We simply call the function im map[$mode] / switchmap trick
// 		$mode = Application::$MODE;
// 		self::$RENDER_CALLMAP[$mode][0] = $this;
// 		return self::$RENDER_CALLMAP[$mode]();
		
		switch (Application::$MODE)
		{
			# Output modes
			case self::RENDER_NIL: return $this->renderNIL();
			case self::RENDER_BINARY: return $this->renderBinary();
			case self::RENDER_CLI: return $this->renderCLI();
			case self::RENDER_PDF: return $this->renderPDF();
			case self::RENDER_XML: return $this->renderXML();
			case self::RENDER_JSON: return $this->renderJSON();
			case self::RENDER_GTK: return $this->renderGTK();
			# Reserved 1-3
			case self::RENDER_WEBSITE: return $this->renderHTML(); # HTML start mode
			# HTML submodes
			case self::RENDER_HTML: return $this->renderHTML();
			case self::RENDER_CARD: return $this->renderCard();
			case self::RENDER_LIST: return $this->renderList();
			case self::RENDER_FORM: return $this->renderForm();
			case self::RENDER_OPTION: return $this->renderOption();
			# HTML table submodes
			case self::RENDER_THEAD: return $this->renderTHead();
			case self::RENDER_ORDER: return $this->renderOrder();
			case self::RENDER_FILTER: return self::EMPTY_STRING;
			case self::RENDER_CELL: return $this->renderHTML();
			case self::RENDER_TFOOT: return $this->renderTFoot();
		}
	}
	
	#############################
	### render default stubs ####
	#############################
	/**
	 * @return string|array
	 */
	public function render() { return $this->renderGDT(); }
	public function renderNIL() : ?string { return null; }
	public function renderBinary() : string { return self::EMPTY_STRING; }
	public function renderCLI() : string { return $this->renderHTML(); }
	public function renderPDF() : string { return $this->renderHTML(); }
	public function renderXML() : string { return $this->renderHTML(); }
	public function renderJSON() { return $this->renderCLI(); }
	public function renderGTK() { return null; }
	public function renderWebsite() : string { return self::EMPTY_STRING; }
	# HTML rendering
	public function renderHTML() : string { return $this->renderVar(); }
	public function renderCard() : string { return $this->renderHTML(); }
	public function renderList() : string { return $this->renderHTML(); }
	public function renderForm() : string { return $this->renderHTML(); }
	public function renderOption() : string { return $this->renderHTML(); }
	# HTML table rendering
	public function renderTHead() : string { return self::EMPTY_STRING; }
	public function renderFilter(GDT_Filter $f) : string { return self::EMPTY_STRING; }
	public function renderCell() : string { return $this->renderHTML(); }
	public function renderTFoot() : string { return $this->renderHTML(); }
	# Various rendering
	public function renderLabel() : string { return GDT::EMPTY_STRING; }
	public function renderLabelText() : string { return GDT::EMPTY_STRING; }
	public function cliIcon() : string { return GDT::EMPTY_STRING; }
	
	#####################
	### Render Helper ###
	#####################
	public function renderVar() : string
	{
		return $this->displayVar($this->getVar());
	}
	
	/**
	 * Display a given var with this GDT. 
	 */
	public function displayVar(string $var=null) : string
	{
		return $var === null ? self::none() : html($var);
	}
	
	/**
	 * Render a null value.
	 */
	public static function none() : string
	{
		static $none;
		if (!$none)
		{
			$none = TextStyle::italic(t('not_specified'));
		}
		return $none;
	}
	
	public function displayChoice($choice) : string
	{
		if (is_string($choice))
		{
			return $choice;
		}
		else
		{
			return $choice->renderOption();
		}
	}
	
	/**
	 * Render this GDT in a specified rendering mode.
	 * This should be the method to use to render a GDT.
	 * The default rendering mode is the initial detected mode.
	 *  
	 * @param int $mode
	 * @return string|array - array for json
	 */
	public function renderMode(int $mode=-1)
	{
		$app = Application::$INSTANCE;
		$mode = $mode < 0 ? Application::$MODE : $mode;
		$old = Application::$MODE;
		$app->mode($mode);
		$result = $this->renderGDT();
		$app->mode($old);
		return $result;
	}
	
	###################
	### Permissions ###
	###################
	public function isHidden() : bool { return false; }
	public function isReadable() : bool { return false; }
	public function isWriteable() : bool { return false; }
	public function isSerializable() : bool { return false; }
	
	################
	### Features ###
	################
	public function isSortable() : bool { return false; }
	public function isOrderable() : bool { return false; }
	public function isDefaultAsc() : bool { return true; }
	public function isSearchable() : bool { return false; }
	public function isFilterable() : bool { return false; }
	
	##################
	### GDO Events ###
	##################
	public function gdoBeforeCreate(GDO $gdo, Query $query) : void {}
	public function gdoBeforeRead  (GDO $gdo, Query $query) : void {}
	public function gdoBeforeUpdate(GDO $gdo, Query $query) : void {}
	public function gdoBeforeDelete(GDO $gdo, Query $query) : void {}
	
	public function gdoAfterCreate(GDO $gdo) : void {}
	public function gdoAfterRead  (GDO $gdo) : void {}
	public function gdoAfterUpdate(GDO $gdo) : void {}
	public function gdoAfterDelete(GDO $gdo) : void {}

	###################
	### Form events ###
	###################
	/**
	 * The method has been succesfully validated.
	 */
	public function onValidated() : void {}

	/**
	 * The form has been executed and submittted.
	 */
	public function onSubmitted() : void {}
	
	################
	### Validate ###
	################
	/**
	 * Validation is a great experience in GDOv7.
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
	 * @see GDT_Select
	 * @see GDT_ComboBox
	 * @see GDT_Enum
	 */
	public function validate($value) : bool
	{
		return true; # all empty GDT does nothing... what can it do? randomly fail?!
	}
	
	public function validated(bool $throw=false) : ?self
	{
// 		$var = $this->getVar();
		$value = $this->getValue();
		if ($this->validate($value))
		{
			$this->onValidated();
			return $this;
		}
		elseif ($throw)
		{
			throw new GDO_ArgException($this);
		}
		return null;
	}
	
	public function hasError() : bool
	{
		return false;
	}
	
	public function error(string $key, array $args = null) : bool
	{
		return false;
	}
	
	public function noError() : self
	{
		return $this;
	}
	
	public function classError() : string
	{
		return self::EMPTY_STRING;
	}
	
	##############
	### Config ###
	##############
	/**
	 * Get the GDTs configuration as JSON array.
	 */
	public function configJSON() : array
	{
		return self::EMPTY_ARRAY;
	}
	
	/**
	 * Render config JSON as html attribute string.
	 */
	public function renderConfigJSON() : string
	{
		$json = $this->configJSON();
		$json = Arrays::filter($json, function($value){
			return $value !== null;
		});
		return json_quote(json($json));
	}
	
	public function htmlConfig() : string
	{
		return sprintf(' data-config=\'%s\'', $this->renderConfigJSON());
	}
	
	##############
	### Filter ###
	##############
	/**
	 * Get the input for this GDTs filter var. 
	 */
	public function filterVar(GDT_Filter $f)
	{
		if ( ($flt = $f->getVar()) && ($name = $this->getName()) )
		{
			$fv = trim((string)@$flt[$name]);
			return $fv === '' ? null : $fv;
		}
		return null;
	}
	
	/**
	 * Check if a GDO is shown for a filter input.
	 */
	public function filterGDO(GDO $gdo, $filterInput) : bool
	{
		$var = $this->getVar();
		if (!is_string($var))
		{
			return false;
		}
		return stripos($var, $filterInput) !== false;
	}
	
	public function filterQuery(Query $query, GDT_Filter $f) : self
	{
		if (null !== ($var = $this->filterVar($f)))
		{
			$var = GDO::escapeSearchS($var);
			$condition = "{$this->getName()} LIKE '%{$var}%'";
			return $this->filterQueryCondition($query, $condition);
		}
		return $this;
	}
	
	public function filterQueryCondition(Query $query, string $condition) : self
	{
		$query->where($condition);
		return $this;
	}
	
	##############
	### Search ###
	##############
	public function searchGDO(string $searchTerm) : bool
	{
		if (null !== ($haystack = $this->getVar()))
		{
			return stripos($haystack, $searchTerm) !== false;
		}
		return false;
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
	 * Get all DB column names for this GDT.
	 * @return string[]
	 */
	public function gdoColumnNames() : array
	{
		return self::EMPTY_ARRAY;
	}
	
	public function gdoColumnDefine() : string
	{
		return self::EMPTY_STRING;
	}
	
	/**
	 * Setup the default label. None by default.
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
	
	public function inputs(array $inputs=null) : self
	{
		return $this;
	}
	
	public function getVar()
	{
		return null;
	}
	
	public function getValue()
	{
		return null;
	}
	
	public function hasChanged() : bool
	{
		return false;
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

	/**
	 * Render the HTML name attribute.
	 */
	public function htmlName() : string
	{
		if ($name = $this->getName())
		{
			return " name=\"{$name}\"";
		}
		return self::EMPTY_STRING;
	}
	
	public function htmlClass() : string
	{
		$class = strtolower($this->gdoShortName());
		return str_replace('_', '-', $class);
	}
	
	public function htmlID() : string
	{
		if ($name = $this->getName())
		{
			return " id=\"{$name}\"";
		}
		return self::EMPTY_STRING;
	}
	
	/**
	 * Provided by WithPHPJQuery.
	 */
	public function htmlAttributes() : string
	{
		return self::EMPTY_STRING;
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
	
	/**
	 * @return string[string]
	 */
	public function getGDOData() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public function setGDOData(array $data) : self
	{
		return $this;
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
	
	public function writeable(bool $writeable) : self
	{
		return $this;
	}
	
	public function isACLCapable() : bool
	{
		return false;
	}
	
	public function reset(bool $removeInput=false) : self
	{
		return $this;
	}
	
	##################
	### Conversion ###
	##################
	public function inputToVar($input) : ?string
	{
		if ($input === null)
		{
			return null;
		}
		if (is_string($input))
		{
			$input = trim($input);
			return $input === '' ? null : $input;
		}
		if ($input instanceof GDT_Method)
		{
			return $input->execute()->render();
		}
		return json_encode($input);
	}
	
	public function toVar($value) : ?string
	{
		return $value;
	}
	
	public function toValue($var = null)
	{
		return $var;
	}
	
	#############
	### Tests ###
	#############
	public function isTestable() : bool
	{
		return true;
	}
	
	/**
	 * Get multiple variants of a plug var.
	 */
	public function plugVars() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	###########
	### CLI ###
	###########
	public function gdoExampleVars() : ?string
	{
		return null;
	}
	
}

# Init
if (GDT::$GDT_DEBUG = deff('GDT_GDO_DEBUG', 0))
{
	# Clear trace log
	Logger::log('gdt', '--- NEW RUN ---');
	Logger::log('gdo', '--- NEW RUN ---');
}
