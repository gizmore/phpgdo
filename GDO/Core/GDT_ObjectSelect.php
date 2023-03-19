<?php
namespace GDO\Core;


use GDO\Util\Arrays;
use GDO\Table\GDT_Filter;

/**
 * A select WithObject trait.
 * It behaves a tiny bit different than GDT_Select, for the selected value.
 * It inits the choices with a call to $table->all()!
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
class GDT_ObjectSelect extends GDT_Select
{
	use WithObject;
	
	public bool $searchable = true;
	public function searchable(bool $searchable): static
	{
		$this->searchable = $searchable;
		return $this;
	}
	
// 	public function multiple(bool $multiple=true): static
// 	{
// 		return parent::multiple($multiple);
// 	}
	
	public function getChoices(): array
	{
		return isset($this->table) ? $this->table->allCached() : GDT::EMPTY_ARRAY;
	}
	
	public function validate($value) : bool
	{
// 		$this->initChoices();
        if ($value === null)
        {
            if ($this->notNull)
            {
                if ($this->getVar())
                {
                    return $this->errorNotFound();
                }
                return $this->errorNull();
            }
        }
		return true;
	}
	
	public function errorNotFound() : bool
	{
	    return $this->error('err_gdo_not_found', [
	        $this->table->gdoHumanName(), html($this->getVar())]);
	}
	
	##############
	### Render ###
	##############
	public function renderForm() : string
	{
		$this->initChoices();
		if ( (isset($this->completionHref)) && (!$this->multiple) )
		{
		    return GDT_Template::php('Core', 'object_completion_form.php', ['field' => $this]);
		}
		return parent::renderForm();
	}
	
	public function renderHTML() : string
	{
		if ($obj = $this->getValue())
		{
			if (is_array($obj))
			{
				$back = array_map(function(GDO $gdo) {
					return $gdo->renderName();
				}, $obj);
				return Arrays::implodeHuman($back);
			}
			return $obj->renderName();
		}
		return '';
	}
	
	public function renderJSON()
	{
		/**
		 * @var $value GDO
		 */
		if ($value = $this->getValue())
		{
			if (is_array($value))
			{
				$json = [];
				foreach ($value as $obj)
				{
					$json[] = $obj->toJSON();
				}
				return $json;
			}
			else
			{
				return $value->toJSON();
			}
		}
	}
	
	public function renderFilter(GDT_Filter $f) : string
	{
		return GDT_Template::php('Core', 'object_filter.php', ['field' => $this, 'f' => $f]);
	}
	
	#############
	### Value ###
	#############
	public function getVar()
	{
		return parent::getVar(); # required to overwrite trait.
	}
	
	public function toVar($value) : ?string
	{
		if ($value === null)
		{
			return null;
		}
		return $this->multiple ? $this->multipleToVar($value) : $value->getID();
	}
	
	public function plugVars() : array
	{
		if (isset($this->table))
		{
			$first = $this->table->select()->first()->exec()->fetchObject();
			if ($first)
			{
				return [
					[$this->name => $first->getID()],
				];
			}
		}
		return GDT::EMPTY_ARRAY;
	}
	
	/**
	 * @param GDO[] $value
	 * @return string
	 */
	public function multipleToVar(array $value)
	{
		$ids = array_map(function(GDO $gdo) {
			return $gdo->getID();
		}, $value);
		return json_encode(array_values($ids));
	}
	
	public function toValue($var = null)
	{
		if ($var)
		{
    		return $this->multiple ? $this->getValueMulti($var) : $this->getValueSingle($var);
	    }
	}
	
	public function getValueSingle(string $id)
	{
		return $this->selectToValue($id);
	}
	
	public function getValueMulti(string $var)
	{
		$back = [];
		
		if (!is_array($var))
		{
		    $var = json_decode($var, true);
		}
		
		foreach ($var as $id)
		{
		    if ($object = $this->table->getById($id))
			{
				$back[$id] = $object;
			}
		}
		return $back;
	}
	
	/**
	 * Try the choices from GDT_Select.
	 * But we are an Object and read from DB!
	 */
	public function selectToValue(string $var = null)
	{
		if ($var !== null)
		{
			if ($value = parent::selectToValue($var))
			{
				return $value;
			}
			return $this->table->getById($var);
		}
	}
	
	##############
	### Config ###
	##############
	private function configJSONSelected()
	{
	    if ($this->multiple)
	    {
	        $selected = [];
	        foreach ($this->getValue() as $value)
	        {
	            $selected[] = array(
	                'id' => $value->getID(),
	                'text' => $value->renderName(),
	                'display' => $value->renderOption(),
	            );
	        }
	    }
	    else
	    {
	        if ($value = $this->getValue())
	        {
    	        $selected = array(
    	            'id' => $value->getID(),
    	            'text' => $value->renderName(),
    	            'display' => $value->renderOption(),
    	        );
	        }
	        else
	        {
	            $selected = array(
	                'id' => $this->emptyVar,
	                'text' => $this->displayEmptyLabel(),
	                'display' => $this->displayEmptyLabel(),
	            );
	        }
	    }
	    return $selected;
	}
	
	public function configJSON() : array
	{
	    return array_merge(parent::configJSON(), [
	        'selected' => $this->configJSONSelected(),
	    ]);
	}
	
}
