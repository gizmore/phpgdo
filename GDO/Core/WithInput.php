<?php
declare(strict_types=1);
namespace GDO\Core;

/** ______________
 * (Need. Input.=|
 *  , (c)gizmore_)
 * [=]
 *°|.|´
 * / \
 *
 * @version 7.0.3
 * @since 7.0.0
 * @author gizmore
 * @license GDOv7-LICENSE
 */
trait WithInput
{

	/**
	 * An input can also be a GDT_Method, for nested expressions.
	 *
	 * @var GDT|string[]
	 */
	public array $inputs;

	/**
	 * Set all inputs to the fixed inputs parameter.
	 *
	 * @param GDT|string[] $inputs
	 */
	public function inputs(?array $inputs): static
	{
		if ($inputs === null)
		{
			unset($this->inputs);
		}
//		elseif (isset($this->inputs))
//		{
//			$this->inputs = array_merge($this->inputs, $inputs);
//		}
		else
		{
			$this->inputs = $inputs;
		}
		return $this;
	}

	public function getInputs(): array
	{
		return $this->inputs ?? GDT::EMPTY_ARRAY;
	}

	public function hasInput(): bool
	{
		if ($name = $this->getName())
		{
			if (isset($this->inputs[$name]))
			{
				return $this->inputs[$name] !== null;
			}
		}
		return false;
	}

	public function getInput(): ?string
	{
		$key = $this->getName();
		return isset($this->inputs[$key]) ? $this->inputs[$key] : null;
	}

	public function addInput(?string $key, $var): static
	{
		$this->inputs = $this->inputs ?? [];
		if ($key)
		{
			$this->inputs[$key] = (string)$var;
		}
		else
		{
			$this->inputs[] = (string)$var;
		}
		return $this;
	}

	/**
	 * @deprecated not type safe
	 */
	public function getInputFor(string $key)
	{
		return isset($this->inputs[$key]) ? $this->inputs[$key] : null;
	}

	public function hasInputFor(string $key): bool
	{
		return isset($this->inputs[$key]);
	}

}
