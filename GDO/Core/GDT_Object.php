<?php
namespace GDO\Core;

/**
 * Object is an integer in the database. Uses WithObject trait for magic.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.4
 * @see WithObject
 */
class GDT_Object extends GDT_UInt
{
	use WithObject;
	use WithCompletion;
	
	public function htmlClass() : string
	{
		return ' gdt-object';
	}
	
	##############
	### Render ###
	##############
	public function renderCell() : string
	{
		if ($obj = $this->getValue())
		{
			return $obj->renderCell();
		}
		if ($var = $this->getVar())
		{
			return $var;
		}
		return '';
	}
	
	public function renderChoice() : string
	 {
	     /** @var $obj GDO **/
		 if ($obj = $this->getValue())
		 {
			 return $obj->renderChoice();
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
	            'display' => json_quote($gdo->renderChoice()),
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
	        'completionHref' => $this->completionHref,
	    ]);
	}
	
}
