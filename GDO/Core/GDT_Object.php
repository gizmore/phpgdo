<?php
namespace GDO\Core;

/**
 * Object is an integer in the database. Uses WithObject trait for magic.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 * @see WithObject
 */
class GDT_Object extends GDT_UInt
{
	use WithObject;
	use WithCompletion;
	
	const MAX_SUGGESTIONS = 10;
	
	public function htmlClass() : string
	{
		return ' gdt-object';
	}
	
	public bool $searchable = true;
	public function searchable(bool $searchable) : self
	{
		$this->searchable = $searchable;
		return $this;
	}
	
	##############
	### Render ###
	##############
	public function renderHTML() : string
	{
		if ($obj = $this->getValue())
		{
			return $obj->renderHTML();
		}
		if ($var = $this->getVar())
		{
			return $var;
		}
		return '';
	}
	
	public function renderOption() : string
	 {
	     /** @var $obj GDO **/
		 if ($obj = $this->getValue())
		 {
			 return $obj->renderOption();
		 }
		 return '';
	 }
	
	 public function renderForm() : string
	{
		if (isset($this->completionHref))
		{
			return GDT_Template::php('Core', 'object_completion_form.php', ['field'=>$this]);
		}
		else
		{
			return GDT_Template::php('Core', 'object_form.php', ['field'=>$this]);
		}
	}
	
	##############
	### Filter ###
	##############
// 	public function filterVar($rq=null) : null
// 	{
// // 		return $this->_getRequestVar("{$rq}[f]", null, $this->filterField ? $this->filterField : $this->name);
// 	}
	
	##############
	### Config ###
	##############
	public function configJSON() : array
	{
	    if ($gdo = $this->getValue())
	    {
	        $selected = [
	            'id' => $gdo->getID(),
	            'text' => $gdo->renderName(),
	            'display' => json_quote($gdo->renderOption()),
	        ];
	    }
	    else 
	    {
	        $selected = [
	            'id' => null,
	            'text' => $this->renderPlaceholder(),
	            'display' => $this->renderPlaceholder(),
	        ];
	    }
	    return array_merge(parent::configJSON(), [
	        'cascade' => $this->cascade,
	        'selected' => $selected,
	        'completionHref' => isset($this->completionHref) ? $this->completionHref : null,
	    ]);
	}

}
