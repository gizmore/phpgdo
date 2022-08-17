<?php
namespace GDO\Core;

use GDO\UI\GDT_Container;
use GDO\UI\WithLabel;
use GDO\Form\WithFormAttributes;

/**
 * A composite is a container that proxies certain methods to all it's fields.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
class GDT_Composite extends GDT_Container
{
	use WithName;
	use WithLabel;
	use WithValue;
	use WithInput;
	use WithError;
	use WithFormAttributes;
	
	public function gdoCompositeFields() : array
	{
		return GDT::EMPTY_ARRAY;
	}
	
	public static function make(string $name=null) : self
	{
		$obj = self::makeNamed($name);
		$obj->addFields(...$obj->gdoCompositeFields());
		return $obj;
	}
	
	###############
	### NotNull ###
	###############
	public function notNull(bool $notNull = true) : self
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->notNull($notNull);
		}
		return $this;
	}
	
	public function inputs(array $inputs=null) : self
	{
		if ($inputs === null)
		{
			unset($this->inputs);
		}
		else
		{
			$this->inputs = $inputs;
		}
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->inputs($inputs);
		}
		return $this;
	}
	
	###################
	### Var / Value ###
	###################
	public function getGDOData() : ?array
	{
		$gdodata = [];
		foreach ($this->getAllFields() as $gdt)
		{
			if ($data = $gdt->getGDOData())
			{
				foreach ($data as $key => $var)
				{
					$gdodata[$key] = $var;
				}
			}
		}
		return $gdodata;
	}
	
	public function setGDOData(array $data) : self
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
		$define = [];
		foreach ($this->getAllFields() as $gdt)
		{
			$define[] = $gdt->gdoColumnDefine();
		}
		return implode("\n", $define);
	}

}
