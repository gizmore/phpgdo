<?php
namespace GDO\Core;

use GDO\UI\GDT_Repeat;

/**
 * Add GDT parameters.
 * Override gdoParameters() in your methods.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 7.0.0
 * @see Method
 */
trait WithParameters
{
	#################
	### Protected ### - Override these
	#################
	/**
	 * Get method parameters.
	 * @return GDT[]
	 */
	public function gdoParameters() : array # @TODO: make gdoParameters() protected
	{
		return GDT::EMPTY_GDT_ARRAY;
	}
	
	##################
	### Parameters ###
	##################
// 	/**
// 	 * Compose all parameters. Not needed yet?
// 	 *
// 	 * @return GDT[]
// 	 */
// 	public function gdoComposeParameters() : array
// 	{
// 		return $this->gdoParameters();
// 	}
	
	public function gdoHasParameter(string $key) : bool
	{
		return isset($this->gdoParameterCache()[$key]);
	}
	
	/**
	 * Get a parameter by key.
	 * If key is an int, get positional parameter N.
	 */
	public function gdoParameter(string $key, bool $validate=true, bool $throw=true) : ?GDT
	{
		if ($gdt = $this->_gdoParameterB($key, $throw))
		{
			if ($validate)
			{
				if (!$gdt->validated())
				{
					if ($throw)
					{
						throw new GDO_Error('err_parameter', [html($key), $gdt->renderError()]);
					}
					return null;
				}
			}
		}
		return $gdt;
	}
	
	private function _gdoParameterB(string $key, bool $throw=true) : ?GDT
	{
		$cache = $this->gdoParameterCache();
		$repeater = null;
		if (isset($cache[$key]))
		{
			return $cache[$key];
		}
		
		elseif (is_numeric($key))
		{
			$pos = -1;
			foreach ($cache as $gdt)
			{
				if ($gdt->isPositional())
				{
					$pos++;
					if ($key == $pos)
					{
						return $gdt;
					}
				}
				
				if ($gdt instanceof GDT_Repeat)
				{
					$repeater = $gdt;
				}
			}
		}
		
		if (isset($repeater))
		{
			return $repeater;
		}
		
		elseif ($throw)
		{
			throw new GDO_Error('err_unknown_parameter', [html($key), $this->gdoHumanName()]);
		}

		return null;
	}

	/**
	 * Get a parameter's GDT db var string.
	 */
	public function gdoParameterVar(string $key, bool $validate=true, bool $throw=true) : ?string
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getVar();
		}
		return null;
	}
	
	public function gdoParameterValue(string $key, bool $validate=true, bool $throw=true)
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getValue();
		}
		return null;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * @var GDT[string]
	 */
	public ?array $parameterCache = null;
	
	/**
	 * @return GDT[string]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
		}
		return $this->parameterCache;
	}
	
	/**
	 * @param GDT[] $params
	 */
	protected function addComposeParameters(array $params) : void
	{
		foreach ($params as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$this->parameterCache[$name] = $gdt;
// 				$gdt->input($this->getInput($name));
			}
		}
	}
	
}
