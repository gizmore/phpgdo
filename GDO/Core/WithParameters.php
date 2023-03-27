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
	 */
	public function gdoParameterVar(string $key, bool $validate = true, bool $throw = true): ?string
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getVar();
		}
		return null;
	}

	/**
	 * Get a parameter by key.
	 * If key is an int, get positional parameter N.
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

	private function gdoParameterB(string $key, bool $throw = true): ?GDT
	{
		$cache = $this->gdoParameterCache();

		if (isset($cache[$key]))
		{
			return $cache[$key];
		}

		if (is_numeric($key))
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

		if ($throw)
		{
			throw new GDO_Error('err_unknown_parameter', [html($key), $this->gdoHumanName()]);
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
		# Add to cache
		foreach ($params as $gdt)
		{
			$this->parameterCache[$gdt->getName()] = $gdt;
		}
//		$this->applyInputComposeParams();
	}

	private function applyInputComposeParams(): void
	{
		# Map positional to now named input
		$pos = -1;
		$newInput = [];
		foreach ($this->gdoParameterCache() as $key => $gdt)
		{
			if ($gdt->isPositional())
			{
				$pos++;
				if (isset($this->inputs[$pos]))
				{
					$newInput[$key] = $this->inputs[$pos];
				}
			}
		}

		# Copy previously already named input
		foreach ($this->getInputs() as $key => $input)
		{
			if (!is_numeric($key))
			{
				$newInput[$key] = $input;
			}
		}
		$this->inputs = $newInput;

		# Apply all input to all GDT
		foreach ($this->gdoParameterCache() as $gdt)
		{
			$gdt->inputs($this->inputs);
		}
	}

	/**
	 * Get method parameters.
	 *
	 * @return GDT[]
	 */
	public function gdoParameters(): array # @TODO: make gdoParameters() protected
	{
		return GDT::EMPTY_ARRAY;
	}

	public function gdoParameterValue(string $key, bool $validate = true, bool $throw = true): int|float|string|array|null|object
	{
		if ($gdt = $this->gdoParameter($key, $validate, $throw))
		{
			return $gdt->getValue();
		}
		return null;
	}

}
