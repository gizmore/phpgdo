<?php
namespace GDO\Core;


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
	
	public function getChoices()
	{
		return $this->table ? $this->table->allCached() : [];
	}
	
	public function initChoices()
	{
		return $this->choices($this->getChoices());
	}
	
	public function validate($value) : bool
	{
		$this->initChoices();
        if ($value === null)
        {
            if ($this->notNull)
            {
                if ($this->getVar())
                {
                    return $this->errorNotFound();
                }
                return $this->errorNotNull();
            }
            return true;
        }
        
        if (!$this->getValue())
        {
            return $this->errorInvalidChoice();
        }
        
		return true;
	}
	
	public function errorNotFound()
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
		if ($this->completionHref)
		{
		    return GDT_Template::php('Core', 'object_completion_form.php', ['field' => $this]);
		}
		return parent::renderForm();
	}
	
	public function renderCell() : string
	{
		if ($obj = $this->getValue())
		{
			if (is_array($obj))
			{
				$back = '';
				foreach ($obj as $gdo)
				{
					$back .= $gdo->displayName();
				}
				return $back;
			}
			return $obj->displayName();
		}
		return $obj;
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
	
	public function renderFilter($f) : string
	{
		return GDT_Template::php('DB', 'filter/object.php', ['field' => $this, 'f' => $f]);
	}
	
	#############
	### Value ###
	#############
	public function getVar() : ?string
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
	
	/**
	 * @param GDO[] $value
	 * @return string
	 */
	public function multipleToVar(array $value)
	{
		$ids = array_map(function($gdo) {
			return $gdo->getID();
		}, $value);
		return json_encode(array_values($ids));
	}
	
	public function toValue(string $var = null)
	{
		if ($var)
		{
    		return $this->multiple ? $this->getValueMulti($var) : $this->getValueSingle($var);
	    }
	}
	
	public function getValueSingle(string $id)
	{
		return $this->table->find($id, false);
	}
	
	public function getValueMulti(string $var)
	{
		$back = [];
		
		if (!is_array($var))
		{
		    $var = json_decode($var);
		}
		
		foreach ($var as $id)
		{
		    if ($object = $this->table->find($id, false))
			{
				$back[$id] = $object;
			}
		}
		return $back;
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
	                'text' => $value->displayName(),
	                'display' => $value->renderChoice(),
	            );
	        }
	    }
	    else
	    {
	        if ($value = $this->getValue())
	        {
    	        $selected = array(
    	            'id' => $value->getID(),
    	            'text' => $value->displayName(),
    	            'display' => $value->renderChoice(),
    	        );
	        }
	        else
	        {
	            $selected = array(
	                'id' => $this->emptyValue,
	                'text' => $this->displayEmptyLabel(),
	                'display' => $this->displayEmptyLabel(),
	            );
	        }
	    }
	    return $selected;
	}
	
	public function configJSON() : array
	{
	    return array_merge(parent::configJSON(), array(
	        'selected' => $this->configJSONSelected(),
	    ));
	}
	
}
