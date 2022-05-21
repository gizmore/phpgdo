<?php
namespace GDO\Core;

/**
 * Add children fields to a GDT.
 * Add $inputs array to allow serving of all children.
 * 
 * The logic i use for parameter filling
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.1
 * @see GDT
 */
trait WithFields
{
	################
	### Instance ###
	################
	public static function makeWith(GDT...$gdt)
	{
		return self::make()->addFields(...$gdt);
	}
	
	##############
	### Inputs ### (Plural)
	##############
	/**
	 * @var GDT|string[]
	 */
	public array $inputs;
	
	public function inputs(array $inputs) : self
	{
		$this->inputs = $inputs;
		return $this;
	}
	
	##############
	### Fields ### More methods available here :)
	##############
	/**
	 * Real tree.
	 * @var GDT[]
	 */
	public array $fields;
	
	public function addFields(GDT...$gdts)
	{
		foreach ($gdts as $gdt)
		{
			$this->addField($gdt);
			if ($gdt->hasFields())
			{
				$this->addFields(...$gdt->getFields());
			}
		}
		return $this;
	}
	
	public function addField(GDT $gdt)
	{
		if (!isset($this->fields))
		{
			$this->fields = [];
		}
		
		if ($name = $gdt->getName())
		{
			$this->fields[$name] = $gdt;
		}
		else
		{
			$this->fields[] = $gdt;
		}
		
		return $this;
	}
	
	
	
// 	/**
// 	 * @var GDT[]
// 	 */
// 	protected array $namecache = [];
	
// 	/**
// 	 * @var GDT[]
// 	 */
// 	protected array $posicache = [];
	
	public function hasFields() : bool
	{
		return count($this->fields) > 0;
	}
	
	public function getFields() : array
	{
		return $this->fields;
	}
	
	public function getField($key) : GDT
	{
		return $this->fields[$key];
	}
	
// 	/**
// 	 * Get the Nth positional field.
// 	 * -1 for the last one, -2 second last.
// 	 * May throw an exception!
// 	 * @return GDT
// 	 */
// 	public function getFieldN(int $n=0) : GDT
// 	{
// 		return $this->fields[$n];
// 	}
	
// 	public function addFields(GDT...$fields) : self
// 	{
// 		foreach ($fields as $gdt)
// 		{
// 			$this->addField($gdt);
// 		}
// 		return $this;
// 	}
	
// 	public function addField(GDT $gdt) : void
// 	{
// 		$this->addFieldB($gdt);
// 		if ($gdt->hasFields())
// 		{
// 			foreach ($gdt->getFields() as $gdt)
// 			{
// 				$this->addField($gdt);
// 			}
// 		}
// 	}
	
// 	protected function addFieldB(GDT $gdt) : void
// 	{
// 		if ($name = $gdt->getName())
// 		{
// 			$this->fields[$name] = $gdt;
// 		}
// 		else
// 		{
// 			$this->fields[] = $gdt;
// 		}
// 	}
	
	###########################
	### Iterate recursively ###
	###########################
	/**
	 * Iterate recusively over the fields with a callback.
	 * If the result is truthy, break the loop early.
	 * 
	 * @param callable $callback
	 * @return self
	 */
	public function withFields(callable $callback)
	{
// 		if ($result = $callback($this))
// 		{
// 			return $result;
// 		}
		foreach ($this->fields as $gdt)
		{
			if ($result = $callback($gdt))
			{
				return $result;
			}
			if ($gdt->hasFields())
			{
				$gdt->withFields($callback);
			}
		}
	}
	
	##############
	### Render ### - 
	##############
	public function render() : string
	{
		return $this->renderGDT();
	}
	
	/**
	 * WithFields, we simply iterate over them and render current mode.
	 * 
	 * @return string
	 */
	public function renderFields() : string
	{
		$rendered = '';
		$this->withFields(function(GDT $gdt) use (&$rendered) {
			$rendered .= $gdt->render();
		});
		return $rendered;
	}
	
	public function renderHTML() : string
	{
		return $this->renderFields();
	}
	
	public function renderCell() : string
	{
		return $this->renderFields();
	}
	
	public function renderForm() : string
	{
		return $this->renderFields();
	}
	
	public function renderCLI() : string
	{
		return $this->renderFields();
	}
	
	public function renderCard() : string
	{
		return $this->renderFields();
	}
	
	public function renderPDF() : string
	{
		return $this->renderFields();
	}
	
	public function renderXML() : string
	{
		return $this->renderFields();
	}
	
	public function renderBinary() : string
	{
		return $this->renderFields();
	}
	
	public function renderJSON()
	{
		$json = [];
		$this->withFields(function(GDT $gdt) use (&$json) {
			if ($gdt->hasName())
			{
				$json[$gdt->getName()] = $gdt->render();
			}
		});
		return $json;
	}
	
}
