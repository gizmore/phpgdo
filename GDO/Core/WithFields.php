<?php
namespace GDO\Core;

use GDO\Table\GDT_Filter;

/**
 * Add children fields to a GDT.
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
	public static function makeWith(GDT...$gdt): static
	{
		return self::make()->addFields(...$gdt);
	}
	
	##############
	### Fields ### More methods available here :)
	##############
	/**
	 * Real tree.
	 * @var GDT[string]
	 */
	public array $fields;
	
	/**
	 * Flattened fields.
	 * @var GDT[]
	 */
	public array $fieldsFlat;
	
	public function setFields(array $fields): static
	{
		unset($this->fields);
		unset($this->fieldsFlat);
		$this->addFields(...array_values($fields));
		return $this;
	}
	
	public function addField(GDT $gdt, GDT $after=null, bool $last=true): static
	{
		return $this->addFieldB($gdt, $after, $last);		
	}
	
	public function addFieldFirst(GDT $gdt): static
	{
		return $this->addFieldB($gdt, null, false);
	}
	
	public function addFieldAfter(GDT $gdt, GDT $after): static
	{
		return $this->addFieldB($gdt, $after, false);
	}

	public function addFieldAfterName(GDT $gdt, string $afterName): static
	{
	    $after = $this->getField($afterName);
	    return $this->addFieldAfter($gdt, $after);
	}
	
// 	public function addFieldAfterNamed(GDT $gdt, string $afterName): static
// 	{
// 		$after = $this->getField($afterName);
// 		return $this->addFieldAfter($gdt, $after);
// 	}
	
	public function addFieldLast(GDT $gdt): static
	{
		return $this->addFieldB($gdt, null, true);
	}
	
	public function addFields(GDT...$gdts): static
	{
		foreach ($gdts as $gdt)
		{
			$this->addField($gdt);
		}
		return $this;
	}
	
	protected function addFieldA(GDT $gdt, GDT $after=null, bool $last=true) : void
	{
		# Init
		if (!isset($this->fields))
		{
			$this->fields = [];
			$this->fieldsFlat = [];
		}
		
		# Do the hard work
		$this->fields = $this->getFieldsSlicy($this->fields, $gdt, $last, $after);
	}
	
	private function getFieldsSlicy(array $fields, GDT $field, bool $last, ?GDT $after)
	{
		# Build 3 slices depending on first, after, last.
		if ($after !== null)
		{
			$i = array_search($field, $fields, true);
			$begn = array_slice($fields, 0, $i + 1);
			$midl = [$field];
			$aftr = array_slice($fields, $i + 1);
		}
		elseif ($last)
		{
			$begn = $fields;
			$midl = [$field];
			$aftr = GDT::EMPTY_ARRAY;
		}
		else # first
		{
			$begn = [$field];
			$midl = array_values($fields);
			$aftr = GDT::EMPTY_ARRAY;
		}
		
		# Build again
		$newfields = [];
		$all = array_merge($begn, $midl, $aftr);
		foreach ($all as $gdt)
		{
			if ($name = $gdt->getName())
			{
				$newfields[$name] = $gdt;
			}
			else
			{
				$newfields[] = $gdt;
			}
		}
		
		# Done :)
		return $newfields;
	}
	
	protected function addFieldB(GDT $gdt, GDT $after=null, bool $last=true): static
	{
		$this->addFieldA($gdt, $after, $last);

		# Add to flatten
		if ($name = $gdt->getName())
		{
			$this->fieldsFlat[$name] = $gdt;
		}
		else
		{
			$this->fieldsFlat[] = $gdt;
		}
		
		# Add children in flatten only
		if ($gdt->hasFields())
		{
			foreach ($gdt->getAllFields() as $gdt)
			{
				if ($name = $gdt->getName())
				{
					$this->fieldsFlat[$name] = $gdt;
				}
				else
				{
					$this->fieldsFlat[] = $gdt;
				}
			}
		}
		
// 		$gdt->inputs($this->getInputs());
		
		return $this;
	}
	
	public function removeFields(): static
	{
		unset($this->fields);
		unset($this->fieldsFlat);
		return $this;
	}
	
	public function removeFieldNamed(string $key, bool $throw=false): static
	{
		if ($field = $this->getField($key, $throw))
		{
			return $this->removeField($field);
		}
		return $this;
	}
	
	public function removeField(GDT $field): static
	{
		if (false !== ($i = array_search($field, $this->fields, true)))
		{
			unset($this->fields[$i]);
		}
		if (false !== ($i = array_search($field, $this->fieldsFlat, true)))
		{
			unset($this->fieldsFlat[$i]);
		}
		return $this;
	}
	
	public function hasFields(bool $ignoreHidden=false) : bool
	{
		if (!$ignoreHidden)
		{
			return count($this->getAllFields()) > 0;
		}
		else
		{
			foreach ($this->getAllFields() as $gdt)
			{
				if (!$gdt->isHidden())
				{
					return true;
				}
			}
			return false;
		}
	}
	
	/**
	 * @return GDT[]
	 */
	public function getFields() : array
	{
		return isset($this->fields) ? $this->fields : GDT::EMPTY_ARRAY;
	}
	
	public function getField(string $key, bool $throw=true) : ?GDT
	{
		if (isset($this->fieldsFlat[$key]))
		{
			return $this->fieldsFlat[$key];
		}
		elseif ($throw)
		{
			throw new GDO_Error('err_unknown_field', [html($key)]);
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Get all fields in a flattened array.
	 * @return GDT[]
	 */
	public function getAllFields() : array
	{
		return isset($this->fieldsFlat) ?
			$this->fieldsFlat :
			GDT::EMPTY_ARRAY;
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
// 				if ($gdt->hasFields())
// 				{
// 					$gdt->withFields($callback);
// 				}
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
	public function renderFields(int $renderMode) : string
	{
		return $this->renderFieldsB($renderMode);
	}
	
	protected function renderFieldsB(int $renderMode) : string
	{
		$app = Application::$INSTANCE;
		$rendered = '';
		$old = Application::$MODE;
		$app->mode($renderMode);
		foreach ($this->getFields() as $gdt)
		{
			$rendered .= $gdt->renderGDT();
		}
		$app->mode($old);
		return $rendered;
	}
	
	####################
	### Render modes ### Proxy them to renderFields().
	####################
// 	public function renderNIL() : ?string { return null; } # hehe
	public function renderBinary() : string { return $this->renderFields(GDT::RENDER_BINARY); }
	public function renderCLI() : string { return $this->renderFields(GDT::RENDER_CLI); }
	public function renderPDF() : string { return $this->renderFields(GDT::RENDER_PDF); }
	public function renderXML() : string { return $this->renderFields(GDT::RENDER_XML); }
// 	public function renderJSON() : { return $this->renderFields(GDT::RENDER_JSON); }
	public function renderGTK() { return $this->renderFields(GDT::RENDER_GTK); }
	public function renderWebsite() : string { return $this->renderFields(GDT::RENDER_WEBSITE); } # Cannot happen
	# html rendering
	public function renderHTML() : string { return $this->renderFields(GDT::RENDER_HTML); }
	public function renderCard() : string { return $this->renderFields(GDT::RENDER_CARD); }
	public function renderList() : string { return $this->renderFields(GDT::RENDER_LIST); }
	public function renderForm() : string { return $this->renderFields(GDT::RENDER_FORM); }
	public function renderOption() : string { return $this->renderFields(GDT::RENDER_OPTION); }
	# html table rendering
// 	public function renderTHead() : string { return $this->renderFields(GDT::RENDER_THEAD); }
	public function renderOrder() : string { return $this->renderFields(GDT::RENDER_ORDER); }
	public function renderFilter(GDT_Filter $f) : string { return $this->renderFields(GDT::RENDER_FILTER); }
	public function renderCell() : string { return $this->renderFields(GDT::RENDER_CELL); }
	public function renderTFoot() : string { return $this->renderFields(GDT::RENDER_TFOOT); }

	public function renderJSON()
	{
		$json = [];
		$this->withFields(function(GDT $gdt) use (&$json) {
			$more = $gdt->renderJSON();
			if ($name = $gdt->getName())
			{
				$json[$name] = $more;
			}
			else
			{
				$json[] = $more;
			}
		});
		return $json;
	}
	
}
