<?php
namespace GDO\Core;

/**
 * Add children fields to a GDT.
 * 
 * @author gizmore
 * @version 7.0.2
 * @since 6.0.1
 * @see GDT
 */
trait WithFields
{
	################
	### Instance ###
	################
	/**
	 * Call unnamed make and add fields.
	 */
	public static function makeWith(GDT...$gdt) : self
	{
		return self::make()->addFields(...$gdt);
	}
	
	##############
	### Fields ### More methods available here :)
	##############
	/**
	 * Real tree.
	 * @var GDT[]
	 */
	public array $fields;
	
	/**
	 * Flattened fields.
	 * @var GDT[]
	 */
	public array $fieldsFlat;
	
	public function addFields(GDT...$gdts) : self
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
	
	public function addField(GDT $gdt) : self
	{
		# Init
		if (!isset($this->fields))
		{
			$this->fields = [];
			$this->fieldsFlat = [];
		}

		# Add the field
		if ($name = $gdt->getName())
		{
			$this->fields[$name] = $gdt;
// 			$this->fieldsFlat[$name] = $gdt;
		}
		else
		{
			$this->fields[] = $gdt;
// 			$this->fieldsFlat[] = $gdt;
		}
		
		# Add children in flatten only
		if ($gdt->hasFields())
		{
			$me = $this;
			$gdt->withFields(function(GDT $gdt) use ($me) {
				if ($name = $gdt->getName())
				{
					$me->fieldsFlat[$name] = $gdt;
				}
				else
				{
					$me->fieldsFlat[] = $gdt;
				}
			});
		}
		
		return $this;
	}
	
	public function hasFields() : bool
	{
		return isset($this->fields) ? count($this->fields) > 0 : false;
	}
	
	public function getFields() : array
	{
		return isset($this->fields) ? $this->fields : GDT::EMPTY_GDT_ARRAY;
	}
	
	public function getField($key) : GDT
	{
		return $this->fields[$key];
	}
	
	/**
	 * Get all fields in a flattened array.
	 * @return GDT[]
	 */
	public function getAllFields() : array
	{
		return isset($this->fieldsFlat) ? $this->fields : GDT::EMPTY_GDT_ARRAY;
	}
	
	###########################
	### Iterate recursively ###
	###########################
	/**
	 * Iterate recusively over the fields with a callback.
	 * If the result is truthy, break the loop early and return the result.
	 */
	public function withFields($callback, bool $returnEarly=false)
	{
		if (isset($this->fields))
		{
			foreach ($this->fields as $gdt)
			{
				if ($result = $callback($gdt))
				{
					if ($returnEarly)
					{
						return $result;
					}
				}
				if ($gdt->hasFields())
				{
					return $gdt->withFields($callback);
				}
			}
		}
	}
	
	/**
	 * Iterate recusively over the fields until we find the one with the key/name/pos.
	 * Then call the callback with it and return the result.
	 * Supports both, named and positional fields.
	 * 
	 * @param string|int $key
	 * @param callable $callback
	 */
	public function withField($key, $callback)
	{
		if (isset($this->fields))
		{
			foreach ($this->fields as $k => $gdt)
			{
				if ($k == $key)
				{
					return $callback($gdt);
				}
				if ($gdt->hasFields())
				{
					$gdt->withFields($callback);
				}
			}
		}
	}
	
	##############
	### Render ### - 
	##############
	public function render()
	{
		return $this->renderGDT();
	}
	
	/**
	 * WithFields, we simply iterate over them and render current mode.
	 */
	public function renderFields() : string
	{
		$rendered = '';
		if (self::$NESTING_LEVEL === 0)
		{
			self::$NESTING_LEVEL++;
			$this->withFields(function(GDT $gdt) use (&$rendered) {
				$rendered .= $gdt->render();
			});
		}
		return $rendered;
	}
	
	public function renderHTML() : string { return $this->renderFields(); }
	public function renderCell() : string { return $this->renderFields(); }
	public function renderForm() : string { return $this->renderFields(); }
	public function renderCLI() : string { return $this->renderFields(); }
	public function renderCard() : string { return $this->renderFields(); }
	public function renderPDF() : string { return $this->renderFields(); }
	public function renderXML() : string { return $this->renderFields(); }
	public function renderBinary() : string { return $this->renderFields(); }

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
