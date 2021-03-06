<?php
namespace GDO\Core;

/**
 * A combobox is a string with additional completion and dropdown.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.0.0
 * @see GDT_Select
 */
class GDT_ComboBox extends GDT_String
{
	use WithCompletion;
	
	###############
	### Choices ###
	###############
	/**
	 * @var string[]
	 */
	public array $choices;
	public function choices(array $choices) : self
	{
		$this->choices = $choices;
		return $this;
	}
	
	############
	### JSON ###
	############
	public function configJSON() : array
	{
	    return array_merge(parent::configJSON(), [
	        'selected' => [
	            'id' => $this->getVar(),
	            'text' => $this->getVar(),
	            'display' => $this->renderChoice(),
	        ],
	        'completionHref' => isset($this->completionHref) ? $this->completionHref : null,
	        'combobox' => 1,
	    ]);
	}
	
	##############
	### Render ###
	##############
	public function renderFilter($f) : string
	{
		if ($this->hasCompletion())
		{
		    return GDT_Template::php('Form', 'combobox_form.php', ['field' => $this, 'f' => $f]);
		}
		else
		{
			return parent::renderFilter($f);
		}
	}
	
}
