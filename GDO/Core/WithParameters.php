<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\UI\GDT_Repeat;

/**
 * Add GDT parameters.
 * Override gdoParameters() in your methods.
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @see Method
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
	 * @throws GDO_ArgException
	 */
	public function gdoParameterVar(string $key, bool $validate = true): ?string
	{
		$gdt = $this->gdoParameter($key, $validate);
		return $gdt ? $gdt->getVar() : null;
	}

	/**
	 * Get a parameter by key.
	 * If key is an int, get positional parameter N.
	 *
	 * @throws GDO_ArgException
	 */
	public function gdoParameter(string $key, bool $validate = true, bool $throw = true): ?GDT
	{
		if ($gdt = $this->gdoParameterB($key, $throw))
		{
			if (($validate) && (!$gdt->validated()))
			{
				if ($throw)
				{
					throw new GDO_ArgException($gdt);
				}
				return null;
			}
		}
		return $gdt;
	}

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
		}
		return $this->parameterCache;
	}

	#############
	### Cache ###
	#############

	/**
	 * @param GDT[] $params
	 */
	protected function addComposeParameters(array $params): void
	{
		$inputs = $this->getInputs();
		foreach ($params as $gdt)
		{
			$this->parameterCache[$gdt->getName()] = $gdt->inputs($inputs);
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
	 * @throws GDO_ArgException
	 */
	public function gdoParameterValue(string $key, bool $validate = true, bool $throw = true): int|float|string|array|null|object
	{
		$gdt = $this->gdoParameter($key, $validate, $throw);
		return $gdt ? $gdt->getValue() : null;
	}

}
