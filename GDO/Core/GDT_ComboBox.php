<?php
namespace GDO\Core;

/**
 * A combobox is a string with additional completion and dropdown.
 * 
 * @see GDT_Select
 * @author gizmore
 * @version 7.0.0
 * @since 6.0.0
 */
class GDT_ComboBox extends GDT_String
{
	use WithCompletion;
	
	/**
	 * @var string[]
	 */
	public $choices = [];
	public function choices(array $choices) : self
	{
		$this->choices = $choices;
		return $this;
	}
	
	public function configJSON() : array
	{
	    return array_merge(parent::configJSON(), array(
	        'selected' => [
	            'id' => $this->getVar(),
	            'text' => $this->getVar(),
	            'display' => $this->display(),
	        ],
	        'completionHref' => $this->completionHref,
	        'combobox' => 1,
	    ));
	}
	
	public function renderForm() : string
	{
	    return GDT_Template::php('Form', 'form/combobox.php', ['field' => $this]);
	}
	
}
