<?php
namespace GDO\Core;

use GDO\Table\GDT_Filter;

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
	            'display' => $this->renderOption(),
	        ],
	        'completionHref' => isset($this->completionHref) ? $this->completionHref : null,
	        'combobox' => 1,
	    ]);
	}
	
	##############
	### Render ###
	##############
	public function renderFilter(GDT_Filter $f) : string
	{
		if (isset($this->completionHref))
		{
		    return GDT_Template::php('Form', 'combobox_form.php', ['field' => $this, 'f' => $f]);
		}
		else
		{
			return parent::renderFilter($f);
		}
	}
	
	public function renderForm() : string
	{
		if (isset($this->completionHref))
		{
			$tVars = [
				'field' => $this,
			];
			return GDT_Template::php('Core', 'combobox_form.php', $tVars);
		}
		return parent::renderForm();
	}

}
