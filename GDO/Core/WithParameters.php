<?php
namespace GDO\Core;

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
	/**
	 * Compose all parameters. Not needed yet?
	 *
	 * @return GDT[]
	 */
	public function gdoComposeParameters() : array
	{
		return $this->gdoParameters();
	}
	
	public function gdoHasParameter(string $key) : bool
	{
		return isset($this->gdoParameterCache()[$key]);
	}
	
	/**
	 * Get a parameter by key.
	 */
	public function gdoParameter(string $key, bool $throw=true) : ?GDT
	{
		$cache = $this->gdoParameterCache();
		if (!($gdt = @$cache[$key]))
		{
			if (is_numeric($key))
			{
				$pos = -1;
				foreach ($cache as $_gdt)
				{
					if ($_gdt->isPositional())
					{
						$pos++;
						if ($pos == $key)
						{
							return $_gdt;
						}
					}
				}
				
			}
		}
		if (!$gdt)
		{
			if ($throw)
			{
				throw new GDO_Error('err_unknown_parameter', [html($key)]);
			}
			return null;
		}
		return $gdt;
	}

	/**
	 * Get a parameter's GDT db var string.
	 */
	public function gdoParameterVar(string $key, bool $validate=false, string $default=null, bool $throw=true) : ?string
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getVar();
		}
		return $default;
	}
	
	public function gdoParameterValue(string $key, bool $validate=false, $default=null, bool $throw=true)
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getValue();
		}
		return $default;
	}
	
	#############
	### Cache ###
	#############
	/**
	 * @var GDT[string]
	 */
	public array $parameterCache;
	
	/**
	 * b
	 * @return GDT[string]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			foreach ($this->gdoComposeParameters() as $gdt)
			{
				if ($name = $gdt->getName())
				{
					$this->parameterCache[$name] = $gdt;
				}
			}
		}
		return $this->parameterCache;
	}
	
}
