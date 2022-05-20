<?php
namespace GDO\Core;

/**
 * Add children fields to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.1
 * @see GDT
 */
trait WithFields
{
	#############
	### Input ###
	#############
	public array $input;
	public function getInput()
	{
		return isset($this->input) ? $this->input : null;
	}
	
	public function input($input = null) : self
	{
		$this->input = $input;
		return $this;
	}
	
	##############
	### Fields ###
	##############
	/**
	 * @var GDT[]
	 */
	protected array $fields = [];
	
	public function getField(string $key) : GDT
	{
		return $this->fields[$key];
	}
	
	public function hasFields() : bool
	{
		return count($this->fields) > 0;
	}
	
	/**
	 * Get all children.
	 * @return GDT[]
	 */
	public function getFields() : array
	{
		return $this->fields;
	}
	
	public function addFields(GDT... $fields) : self
	{
		foreach ($fields as $gdt)
		{
			$this->addField($gdt);
		}
		return $this;
	}
	
	public function addField(GDT $field = null) : self
	{
		if ($field)
		{
			if ($field->hasName())
			{
				$this->fields[$field->getName()] = $field;
			}
			else
			{
				$this->fields[] = $field;
			}
		}
		return $this;
	}
	
	/**
	 * @return GDT[]
	 */
	public function getFieldsRec() : array
	{
		return $this->_getFieldsRec($this);
	}
	
	private function _getFieldsRec(GDT $gdt) : array
	{
		$fields = [];
		foreach ($gdt->getFields() as $_gdt)
		{
			if ($_gdt->hasName())
			{
				$fields[$_gdt->name] = $_gdt;
			}
			else
			{
				$fields[] = $_gdt;
			}
			if (isset($_gdt->fields))
			{
				$fields = array_merge($fields,
					$this->_getFieldsRec($_gdt)
					);
			}
		}
		return $fields;
	}
	
	/**
	 * Iterate recusively over the fields with a callback.
	 * @param callable $callback
	 * @return self
	 */
	public function withFields(callable $callback) : self
	{
		foreach ($this->getFields() as $gdt)
		{
			$callback($gdt);
			if ($gdt->hasFields())
			{
				$gdt->withFields($callback);
			}
		}
		return $this;
	}
	
	###################
	### Instanciate ###
	###################
	public static function makeWith(GDT... $fields) : self
	{
		return parent::make()->addFields(...$fields);
	}
	
	public static function makeNamedWith(string $name = null, GDT... $fields) : self
	{
		return parent::make($name)->addFields(...$fields);
	}
	
	##############
	### Render ###
	##############
	public function render() : string
	{
		return $this->renderGDT();
	}
	
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
