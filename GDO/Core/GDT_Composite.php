<?php
declare(strict_types=1);
namespace GDO\Core;

use GDO\Form\WithFormAttributes;
use GDO\UI\GDT_Container;
use GDO\UI\WithLabel;

/**
 * A composite is a container that proxies certain methods to all it's fields.
 *
 * @version 7.0.3
 * @since 7.0.1
 * @author gizmore
 */
class GDT_Composite extends GDT_Container
{

	use WithLabel;
	use WithValue;
	use WithInput { inputs as private inputsB; }
	use WithError;
	use WithFormAttributes;

	public static function make(string $name = null): static
	{
		$obj = self::makeNamed($name);
		$obj->addFields(...$obj->gdoCompositeFields());
		return $obj;
	}

	public function gdoCompositeFields(): array
	{
		return GDT::EMPTY_ARRAY;
	}

	###############
	### NotNull ###
	###############

	public function notNull(bool $notNull = true): self
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->notNull($notNull);
		}
		return $this;
	}

	public function gdo(?GDO $gdo): static
	{
		array_map(function (GDT $gdt) use ($gdo)
		{
			$gdt->gdo($gdo);
		}, $this->getAllFields());
		return $this;
	}

	###################
	### Var / Value ###
	###################

	public function setGDOData(array $data): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->setGDOData($data);
		}
		return $this;
	}


	public function getGDOData(): array
	{
		$gdodata = [];
		foreach ($this->getAllFields() as $gdt)
		{
			$data = $gdt->getGDOData();
			foreach ($data as $key => $var)
			{
				$gdodata[$key] = $var;
			}
		}
		return $gdodata;
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function validated(bool $throw = false): ?static
	{
		$valid = true;
		$inputs = $this->getInputs();
		foreach ($this->getAllFields() as $gdt)
		{
			if (!$gdt->inputs($inputs)->validated())
			{
				$valid = false;
				if ($throw)
				{
					throw new GDO_ArgError($gdt);
				}
			}
		}
		return $valid ? $this : null;
	}

	public function inputs(?array $inputs): static
	{
		$this->inputsB($inputs);
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->inputs($inputs);
		}
		return $this;
	}

	#############
	### Proxy ###
	#############

	public function blankData(): array
	{
		$data = [];
		foreach ($this->getAllFields() as $gdt)
		{
			foreach ($gdt->blankData() as $key => $var)
			{
				$data[$key] = $var;
			}
		}
		return $data;
	}

	##########
	### DB ###
	##########

	public function gdoColumnDefine(): string
	{
		$defines = array_map(function (GDT $gdt)
		{
			return $gdt->gdoColumnDefine();
		}, $this->getAllFields());
		return implode(",\n", $defines);
	}

	public function plugVars(): array
	{
		$plugs = [];
		foreach ($this->getAllFields() as $gdt)
		{
			$plugs = array_merge($plugs, $gdt->plugVars());
		}
		return $plugs;
	}

	############
	### Test ###
	############

	public function configJSON(): array
	{
		return [
			'name' => $this->getName(),
		];
	}

	##############
	### Render ###
	##############

	public function writeable(bool $writeable): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->writeable($writeable);
		}
		return $this;
	}

	public function renderError(): string
	{
		$errors = [];
		foreach ($this->getAllFields() as $gdt)
		{
			if ($gdt->hasError())
			{
				$errors[] = "{$gdt->getName()}: {$gdt->renderError()}";
			}
		}
		return implode(' - ', $errors);
	}

	public function tooltip(string $key, array $args = null): self
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->tooltip($key, $args);
		}
		return $this;
	}

}
