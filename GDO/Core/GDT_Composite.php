<?php
namespace GDO\Core;

use GDO\UI\GDT_Container;
use GDO\UI\WithLabel;
use GDO\Form\WithFormAttributes;

/**
 * A composite is a container that proxies certain methods to all it's fields.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 7.0.1
 */
class GDT_Composite extends GDT_Container
{
	use WithLabel;
	use WithValue;
	use WithInput;
	use WithError;
	use WithFormAttributes;
	
	public function gdoCompositeFields() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public static function make(string $name=null): static
	{
		$obj = self::makeNamed($name);
		$obj->addFields(...$obj->gdoCompositeFields());
		return $obj;
	}
	
	###############
	### NotNull ###
	###############
	public function notNull(bool $notNull = true): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->notNull($notNull);
		}
		return $this;
	}
	
	public function inputs(?array $inputs): static
	{
		parent::inputs($inputs);
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->inputs($inputs);
		}
		return $this;
	}
	
	###################
	### Var / Value ###
	###################
	public function gdo(GDO $gdo = null): static
	{
		array_map(function(GDT $gdt) use ($gdo) {
			$gdt->gdo($gdo);
		}, $this->getAllFields());
		return $this;
	}
	
	public function getGDOData() : array
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
	
	public function setGDOData(array $data): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->setGDOData($data);
		}
		return $this;
	}
	
	public function validated(bool $throw=false) : ?self
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
					throw new GDO_ArgException($gdt);
				}
			}
		}
		return $valid ? $this : null;
	}
	
	public function getValue()
	{
		return parent::getValue();
	}
	
	#############
	### Proxy ###
	#############
	public function writeable(bool $writeable): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->writeable($writeable);
		}
		return $this;
	}
	
	##########
	### DB ###
	##########
	public function blankData() : array
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
	
	public function gdoColumnDefine() : string
	{
		$defines = array_map(function(GDT $gdt) {
			return $gdt->gdoColumnDefine();
		}, $this->getAllFields());
		return implode(",\n", $defines);
	}

	############
	### Test ###
	############
	public function plugVars() : array
	{
		$plugs = [];
		foreach ($this->getAllFields() as $gdt)
		{
			$plugs = array_merge($plugs, $gdt->plugVars());
		}
		return $plugs;
	}
	
	##############
	### Render ###
	##############
	public function renderError() : string
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
	
	public function configJSON() : array
	{
		return [
			'name' => $this->getName(),
		];
	}
	
	public function tooltip(string $key, array $args=null): static
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->tooltip($key, $args);
		}
		return $this;
	}

}
