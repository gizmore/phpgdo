<?php
namespace GDO\Core;

use GDO\UI\GDT_Error;
use GDO\UI\GDT_Success;
use GDO\Util\Strings;

/**
 * Abstract baseclass for all methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 3.0.1
 */
abstract class Method
{
	use WithModule;
	
	public abstract function execute() : GDT;
	
	###################
	### Alias Cache ###
	###################
	
	/**
	 * @var Method[]
	 */
	public static array $CLI_ALIASES = [];
	
	public static function addCLIAlias(string $alias, string $className) : void
	{
		self::$CLI_ALIASES[$alias] = $className;
	}
	
	/**
	 * Get a method by cli convention. Aliases first, then module DOT method.
	 * 
	 * @param string $alias
	 * @return Method
	 */
	public static function getMethod(string $alias) : self
	{
		if (isset(self::$CLI_ALIASES[$alias]))
		{
			return self::$CLI_ALIASES[$alias];
		}
		else
		{
			$moduleName = Strings::substrTo($alias, '.');
			$methodName = Strings::substrFrom($alias, '.');
			$module = ModuleLoader::instance()->getModule($moduleName);
			return $module->getMethod($methodName);
		}
	}

	###################
	### Instanciate ###
	###################
	public static function make() : self
	{
		return new static();
	}
	
	protected function __construct()
	{
	}
	
	##################
	### Parameters ###
	##################
	/**
	 * @return GDT[]
	 */
	public function gdoParameters() : array
	{
		return [];
	}
	
	/**
	 * @var GDT[]
	 */
	private array $parameterCache;
	
	/**
	 * @return GDT[]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			foreach ($this->gdoParameters() as $gdt)
			{
				$this->parameterCache[$gdt->name] = $gdt;
			}
		}
		return $this->parameterCache;
	}
	
	public function gdoParameter($name) : GDT
	{
		return $this->gdoParameterCache()[$name];
	}
	
	public function gdoParameterVar($name) : string
	{
		return $this->gdoParameter($name)->var;
	}
	
	public function gdoParameterValue($name) : string
	{
		return $this->gdoParameter($name)->getValue();
	}
	
	public function setInputs(array $inputs)
	{
		$i = 0;
		/**
		 * @var GDT[] $positional
		 */
		$positional = [];
		/**
		 * @var GDT[] $namedional
		 */
		$namedional = [];
		
		foreach ($this->gdoParameterCache() as $gdt)
		{
			if ($gdt->isPositional())
			{
				$positional[] = $gdt;
			}
			else
			{
				$namedional[$gdt->getName()] = $gdt;
			}
		}

		foreach ($inputs as $key => $input)
		{
			if (is_numeric($key))
			{
				$positional[$i++]->input($input);
			}
			else
			{
				$namedional[$key]->input($input);
			}
		}
	}
	
	#############
	### Error ###
	#############
	public function message($key, array $args = null, int $code = 200, bool $log = true) : GDT
	{
		return $this->success($key, args, $code, $log);
	}
	
	public function success($key, array $args = null, int $code = 200, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if ($log)
		{
			Logger::logMessage(ten($key, $args));
		}
		return GDT_Success::make()->titleRaw($this->getModuleName())->text($key, $args);
	}
	
	public function error($key, array $args = null, int $code = 409, bool $log = true) : GDT
	{
		Application::setResponseCode($code);
		if ($log)
		{
			Logger::logError(ten($key, $args));
		}
		return GDT_Error::make()->titleRaw($this->getModuleName())->text($key, $args);
	}

	################
	### Template ###
	################
	public function templatePHP(string $path, array $tVars=null) : GDT_Template
	{
		return GDT_Template::make()->template($this->getModuleName(), $path, $tVars);
	}
	
}
