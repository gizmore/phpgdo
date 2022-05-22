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
	 * 
	 * @throws GDO_Error
	 * @throws GDO_ArgException
	 */
	public function gdoParameter(string $key, bool $validate=false, bool $throw=true) : ?GDT
	{
		if (!$this->gdoHasParameter($key))
		{
			if ($throw)
			{
				throw new GDO_Error('err_unknown_parameter', [html($key)]);
			}
			return null;
		}

		$gdt = $this->gdoParameterCache()[$key];
		
		if ($validate)
		{
			$value = $gdt->getValue();
			if (!$gdt->validate($value))
			{
				if ($throw)
				{
					throw new GDO_ArgException($gdt);
				}
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
	 * @return GDT[string]
	 */
	public function &gdoParameterCache() : array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			foreach ($this->gdoComposeParameters() as $gdt)
			{
				$this->parameterCache[$gdt->name] = $gdt;
			}
		}
		return $this->parameterCache;
	}
	
}
