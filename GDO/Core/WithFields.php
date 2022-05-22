<?php
namespace GDO\Core;

/**
 * Add children fields to a GDT.
 * Add $inputs array to allow serving of all children.
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
	
	public function hasFields() : bool
	{
		return isset($this->fields) ? count($this->fields) > 0 : false;
	}
	
	public function getFields() : array
	{
		return isset($this->fields) ? $this->fields : GDT::EMPTY_ARRAY;
	}
	
	public function getField($key) : GDT
	{
		return $this->fields[$key];
	}
	
	###########################
	### Iterate recursively ###
	###########################
	/**
	 * Iterate recusively over the fields with a callback.
	 * If the result is truthy, break the loop early and return the result.
	 */
	public function withFields($callback)
	{
		if (isset($this->fields))
		{
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
	public function render() : string
	{
		return $this->renderGDT();
	}
	
	/**
	 * WithFields, we simply iterate over them and render current mode.
	 */
	public function renderFields() : string
	{
		$rendered = '';
		$this->withFields(function(GDT $gdt) use (&$rendered) {
			$rendered .= $gdt->render();
		});
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
