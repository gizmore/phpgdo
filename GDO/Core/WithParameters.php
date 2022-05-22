<?php
namespace GDO\Core;

/**
 * Add GDT parameters.
 * Only named GDT are allowed.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 * @see Method
 */
trait WithParameters
{
	/**
	 * Get method parameters.
	 * 
	 * @return GDT[]
	 */
	public function gdoParameters() : array # @TODO: make gdoParameters() protected
	{
		return GDT::EMPTY_ARRAY;
	}
	
	/**
	 * Compose all parameters.
	 * 
	 * @return GDT[]
	 */
	public function gdoComposeParameters() : array
	{
		return $this->gdoParameters();
	}
	
	public array $parameterCache;
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
