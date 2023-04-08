<?php
declare(strict_types=1);
namespace GDO\Core;

/**
 * Adds error annotations to a GDT.
 *
 * @version 7.0.3
 * @since 6.1.0
 * @author gizmore
 */
trait WithError
{

	public string $errorRaw;

	public string $errorKey;

	public ?array $errorArgs;


	/**
	 * Unlike the chain pattern, this returns false!
	 */
	public function error(string $key, array $args = null): bool
	{
		$this->errorKey = $key;
		$this->errorArgs = $args;
		unset($this->errorRaw);
		return false;
	}

	public function noError(): static
	{
		unset($this->errorRaw);
		unset($this->errorKey);
		$this->errorArgs = null;
		return $this;
	}

	public function errorRaw(string $message): bool
	{
		unset($this->errorKey);
		$this->errorArgs = null;
		$this->errorRaw = $message;
		return false;
	}

	/**
	 * Render error message as html form field error annotation.
	 */
	public function htmlError(): string
	{
		return $this->hasError() ?
			('<div class="gdt-form-error">' . $this->renderError() . '</div>') :
			GDT::EMPTY_STRING;
	}

	public function hasError(): bool
	{
		if (isset($this->errorKey) || isset($this->errorRaw))
		{
			return true;
		}
		if ($this->hasFields())
		{
			foreach ($this->getAllFields() as $gdt)
			{
				if ($gdt->hasError())
				{
					return true;
				}
			}
		}
		return false;
	}

	public function renderError(): string
	{
		if (isset($this->errorRaw))
		{
			return $this->errorRaw;
		}
		if (isset($this->errorKey))
		{
			return t($this->errorKey, $this->errorArgs);
		}
		$errors = [];
		foreach ($this->getFields() as $gdt)
		{
			if ($gdt->hasError())
			{
				$errors[] = $gdt->renderError();
			}
		}
		if ($errors)
		{
			return implode(" ", $errors);
		}
		return GDT::EMPTY_STRING;
	}

}
