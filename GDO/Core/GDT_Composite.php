<?php
namespace GDO\Core;

use GDO\UI\GDT_Container;
use GDO\UI\WithLabel;

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
	
	public function gdoCompositeFields() : array
	{
		return GDT::EMPTY_GDT_ARRAY;
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
	
	###################
	### Var / Value ###
	###################
	public function getGDOData() : array
	{
		$data = [];
		foreach ($this->getAllFields() as $gdt)
		{
			foreach ($gdt->getGDOData() as $key => $var)
			{
				$data[$key] = $var;
			}
		}
		return $data;
	}
	
	public function setGDOData(array $data) : self
	{
		foreach ($this->getAllFields() as $gdt)
		{
			$gdt->setGDOData($data);
		}
		return $this;
	}
	
	public function validated() : ?self
	{
		$valid = true;
		foreach ($this->getAllFields() as $gdt)
		{
			if (!$gdt->inputs($this->inputs)->validated())
			{
				$valid = false;
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
	
// 	public function gdoColumnNames()
// 	{
// 		return [
// 			"{$this->name}_lat",
// 			"{$this->name}_lng",
// 			];
// 	}
	
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
