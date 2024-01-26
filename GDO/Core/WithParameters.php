<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Form\MethodForm;
use GDO\UI\GDT_Repeat;

/**
 * Add GDT parameters.
 * Override gdoParameters() in your methods.
 * Use gdoParamerterCache() to get all params (from everything in a method, including forms and tables and stuff)
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see Method
 * @see MethodForm
 */
trait WithParameters
{

	#################
	### Protected ### - Override these
	#################
	/**
	 * @var GDT[]
	 */
	public array $parameterCache;

	##################
	### Parameters ###
	##################

	/**
	 * Get a parameter's GDT db var string.
	 *
	 * @throws GDO_ArgError
	 */
	public function gdoParameterVar(string $key, bool $validate = true): ?string
	{
		return $this->gdoParameter($key, $validate)?->getVar();
	}

	/**
	 * Get a parameter by key.
	 * If key is an int, get positional parameter N.
	 *
	 * @throws GDO_ArgError
	 */
	public function gdoParameter(string $key, bool $validate = true): ?GDT
	{
		if ($gdt = $this->gdoParameterB($key))
		{
			if (($validate) && (!$gdt->validated()))
			{
				throw new GDO_ArgError($gdt);
			}
			return $gdt;
		}
		return null;
	}

	/**
	 * Get a parameter by either index, name, or the first/only repeater.
	 */
	private function gdoParameterB(string $key): ?GDT
	{
		$cache = $this->gdoParameterCache();

		if (isset($cache[$key]))
		{
			return $cache[$key];
		}

		if (is_numeric($key))
		{
			$pos = 0;
			foreach ($cache as $gdt)
			{
				if ($gdt->isPositional())
				{
					if ($key == $pos++)
					{
						return $gdt;
					}
				}
				if ($gdt instanceof GDT_Repeat)
				{
					return $gdt;
				}
			}
		}

		foreach ($cache as $gdt)
		{
			if ($gdt->getParameterAlias() === $key)
			{
				return $gdt;
			}
		}

		return null;
	}

	/**
	 * @return GDT[]
	 */
	public function &gdoParameterCache(): array
	{
		if (!isset($this->parameterCache))
		{
			$this->parameterCache = [];
			$this->addComposeParameters($this->gdoParameters());
            $this->afterAddCompose();
		}
		return $this->parameterCache;
	}

    protected function afterAddCompose(): void
    {

    }

	#############
	### Cache ###
	#############

	/**
	 * Populate the parameter cache with GDTs.
	 * @param GDT[] $params
	 */
	protected function addComposeParameters(array $params): void
	{
		$inputs = $this->getInputs();
		foreach ($params as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$this->parameterCache[$name] = $gdt->inputs($inputs);
			}
		}
	}


	/**
	 * Get method parameters.
	 *
	 * @return GDT[]
	 */
	public function gdoParameters(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function gdoParameterValue(string $key, bool $validate = true, bool $throw = true): mixed
	{
        $gdt = $this->gdoParameter($key, $validate);
		return $gdt?->getValue();
	}

}
