<?php
namespace GDO\Core;

/**
 * Add children fields to a GDT.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.1
 */
trait WithFields
{
	protected array $fields = [];
	
	public function getField(string $key)
	{
		return $this->fields[$key];
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
	
	public function addField(GDT $field) : self
	{
		if ($field->hasName())
		{
			$this->fields[$field->getName()] = $field;
		}
		else
		{
			$this->fields[] = $field;
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
	
}
