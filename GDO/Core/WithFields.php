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
	
	public function addField(GDT $field = null) : self
	{
		if ($field === null)
		{
			return $this;
		}
		elseif ($field->hasName())
		{
			$this->fields[$field->getName()] = $field;
		}
		else
		{
			$this->fields[] = $field;
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
		$rendered = '';
		$this->withFields(function(GDT $gdt) use(&$rendered) {
			$rendered .= $gdt->render();
		});
		return $rendered;
	}

}
